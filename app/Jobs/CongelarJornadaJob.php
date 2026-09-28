<?php

namespace App\Jobs;

use App\Models\Alineacion;
use App\Models\Jornada;
use App\Models\Liguilla;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class CongelarJornadaJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Número de intentos del Job antes de fallar.
     */
    public int $tries = 3;

    /**
     * Tiempo máximo de ejecución en segundos.
     */
    public int $timeout = 300;

    /**
     * Crear una nueva instancia del Job.
     */
    public function __construct(public Jornada $jornada)
    {
    }

    /**
     * Ejecutar el Job de congelación de alineaciones por lotes para evitar saturación de memoria.
     */
    public function handle(): void
    {
        $torneo = $this->jornada->loadMissing('torneo')->torneo;

        if (!$torneo) {
            Log::warning("CongelarJornadaJob: La jornada {$this->jornada->id} no tiene torneo asociado.");
            return;
        }

        // Procesar liguillas en chunks de 50 para mantener consumo de memoria O(1)
        Liguilla::where('torneo_id', $torneo->id)->chunkById(50, function ($liguillas) {
            foreach ($liguillas as $liguilla) {
                $this->congelarLiguilla($liguilla);
            }
        });

        // Marcar la jornada como congelada
        $this->jornada->alineaciones_congeladas = true;
        $this->jornada->save();

        Log::info("CongelarJornadaJob: Alineaciones congeladas exitosamente para la jornada {$this->jornada->id}.");
    }

    /**
     * Congela las alineaciones de una liguilla usando chunkById y bulk inserts.
     */
    protected function congelarLiguilla(Liguilla $liguilla): void
    {
        Alineacion::where('liguilla_id', $liguilla->id)
            ->whereNull('jornada_id')
            ->with('jugadores')
            ->chunkById(100, function ($baseAlineaciones) use ($liguilla) {
                DB::transaction(function () use ($baseAlineaciones, $liguilla) {
                    $now = now();

                    foreach ($baseAlineaciones as $baseAlineacion) {
                        // Idempotencia: Verificar si ya existe la foto de la jornada para este usuario
                        $yaExiste = Alineacion::where('liguilla_id', $liguilla->id)
                            ->where('user_id', $baseAlineacion->user_id)
                            ->where('jornada_id', $this->jornada->id)
                            ->exists();

                        if ($yaExiste) {
                            continue;
                        }

                        // Crear cabecera de alineación congelada
                        $alineacionJornada = Alineacion::create([
                            'user_id'     => $baseAlineacion->user_id,
                            'liguilla_id' => $liguilla->id,
                            'jornada_id'  => $this->jornada->id,
                            'formacion'   => $baseAlineacion->formacion,
                        ]);

                        // Preparar filas para Bulk Insert en la tabla pivote alineacion_jugador
                        $pivotRows = [];
                        foreach ($baseAlineacion->jugadores as $jugador) {
                            $pivotRows[] = [
                                'alineacion_id' => $alineacionJornada->id,
                                'jugador_id'    => $jugador->id,
                                'puntos'        => 0,
                                'created_at'    => $now,
                                'updated_at'    => $now,
                            ];
                        }

                        if (!empty($pivotRows)) {
                            DB::table('alineacion_jugador')->insert($pivotRows);
                        }
                    }
                });
            });
    }
}
