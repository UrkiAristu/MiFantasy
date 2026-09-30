<?php

namespace App\Jobs;

use App\Models\Jornada;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class RecalcularPuntosJornadaJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Número de intentos del Job antes de fallar.
     */
    public int $tries = 3;

    /**
     * Tiempo máximo de ejecución en segundos.
     */
    public int $timeout = 120;

    /**
     * ID de la jornada a recalcular.
     */
    public int $jornadaId;

    /**
     * Crear una nueva instancia del Job.
     */
    public function __construct(int|Jornada $jornada)
    {
        $this->jornadaId = $jornada instanceof Jornada ? $jornada->id : $jornada;
    }

    /**
     * Ejecutar el Job de cálculo masivo y atómico de puntos.
     */
    public function handle(): void
    {
        $jornada = Jornada::with('torneo')->find($this->jornadaId);

        if (! $jornada || ! $jornada->torneo) {
            Log::warning("RecalcularPuntosJornadaJob: Jornada {$this->jornadaId} o torneo no encontrados.");

            return;
        }

        $torneoId = $jornada->torneo_id;

        DB::transaction(function () use ($torneoId) {
            // 1. Bulk Update atómico para alineacion_jugador en esta jornada
            $this->actualizarPuntosAlineaciones($this->jornadaId);

            // 2. Bulk Update atómico para liguilla_usuario (clasificación global del torneo)
            $this->actualizarPuntosGlobalesLiguillas($torneoId);
        });

        Log::info("RecalcularPuntosJornadaJob: Puntos recalculados masivamente para jornada {$this->jornadaId} y torneo {$torneoId}.");
    }

    /**
     * Actualiza masivamente los puntos de alineacion_jugador para la jornada dada en O(1) consultas SQL.
     */
    protected function actualizarPuntosAlineaciones(int $jornadaId): void
    {
        $driver = DB::connection()->getDriverName();

        if ($driver === 'mysql') {
            DB::statement('
                UPDATE alineacion_jugador aj
                JOIN alineaciones a ON a.id = aj.alineacion_id
                LEFT JOIN (
                    SELECT e.jugador_id, SUM(e.puntos) AS total_puntos
                    FROM estadisticas e
                    JOIN partidos p ON p.id = e.partido_id
                    WHERE p.jornada_id = ?
                    GROUP BY e.jugador_id
                ) stats ON stats.jugador_id = aj.jugador_id
                SET aj.puntos = COALESCE(stats.total_puntos, 0)
                WHERE a.jornada_id = ?
            ', [$jornadaId, $jornadaId]);
        } else {
            // Compatibilidad SQLite / testing
            DB::statement('
                UPDATE alineacion_jugador
                SET puntos = COALESCE((
                    SELECT SUM(e.puntos)
                    FROM estadisticas e
                    JOIN partidos p ON p.id = e.partido_id
                    WHERE p.jornada_id = ? AND e.jugador_id = alineacion_jugador.jugador_id
                ), 0)
                WHERE alineacion_id IN (
                    SELECT id FROM alineaciones WHERE jornada_id = ?
                )
            ', [$jornadaId, $jornadaId]);
        }
    }

    /**
     * Actualiza masivamente los puntos acumulados en liguilla_usuario para todas las liguillas del torneo.
     */
    protected function actualizarPuntosGlobalesLiguillas(int $torneoId): void
    {
        $driver = DB::connection()->getDriverName();

        if ($driver === 'mysql') {
            DB::statement('
                UPDATE liguilla_usuario lu
                JOIN liguillas l ON l.id = lu.liguilla_id
                LEFT JOIN (
                    SELECT a.liguilla_id, a.user_id, SUM(aj.puntos) AS total_puntos
                    FROM alineaciones a
                    JOIN alineacion_jugador aj ON aj.alineacion_id = a.id
                    WHERE a.jornada_id IS NOT NULL
                    GROUP BY a.liguilla_id, a.user_id
                ) calc ON calc.liguilla_id = lu.liguilla_id AND calc.user_id = lu.user_id
                SET lu.puntos = COALESCE(calc.total_puntos, 0)
                WHERE l.torneo_id = ?
            ', [$torneoId]);
        } else {
            // Compatibilidad SQLite / testing
            DB::statement('
                UPDATE liguilla_usuario
                SET puntos = COALESCE((
                    SELECT SUM(aj.puntos)
                    FROM alineaciones a
                    JOIN alineacion_jugador aj ON aj.alineacion_id = a.id
                    WHERE a.liguilla_id = liguilla_usuario.liguilla_id
                      AND a.user_id = liguilla_usuario.user_id
                      AND a.jornada_id IS NOT NULL
                ), 0)
                WHERE liguilla_id IN (
                    SELECT id FROM liguillas WHERE torneo_id = ?
                )
            ', [$torneoId]);
        }
    }
}
