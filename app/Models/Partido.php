<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\DB;

class Partido extends Model
{
    use HasFactory;

    protected $table = 'partidos';

    public $timestamps = true;

    protected $guarded = [];

    protected $casts = [
        'eventos' => 'array',
        'fecha_partido' => 'datetime',
    ];

    /**
     * @return BelongsTo<Jornada, $this>
     */
    public function jornada(): BelongsTo
    {
        return $this->belongsTo(Jornada::class);
    }

    /**
     * @return BelongsTo<Equipo, $this>
     */
    public function equipoLocal(): BelongsTo
    {
        return $this->belongsTo(Equipo::class, 'equipo_local_id');
    }

    /**
     * @return BelongsTo<Equipo, $this>
     */
    public function equipoVisitante(): BelongsTo
    {
        return $this->belongsTo(Equipo::class, 'equipo_visitante_id');
    }

    public function getEquiposAttribute()
    {
        return collect([$this->equipoLocal, $this->equipoVisitante])
            ->filter();
    }

    /**
     * @return HasMany<Estadistica, $this>
     */
    public function estadisticas(): HasMany
    {
        return $this->hasMany(Estadistica::class);
    }

    public function actualizarEstadisticas()
    {
        // 1) Borrar todo para este partido
        Estadistica::where('partido_id', $this->id)->delete();

        // 2) Resultado base
        $localGoles = $this->goles_local;
        $visitanteGoles = $this->goles_visitante;

        if ($localGoles === null || $visitanteGoles === null) {
            return;
        }

        $resultadoLocal = $localGoles > $visitanteGoles ? 'ganado' : ($localGoles < $visitanteGoles ? 'perdido' : 'empatado');
        $resultadoVisitante = $localGoles > $visitanteGoles ? 'perdido' : ($localGoles < $visitanteGoles ? 'ganado' : 'empatado');
        $puntosLocal = $resultadoLocal === 'ganado' ? 3 : ($resultadoLocal === 'empatado' ? 1 : 0);
        $puntosVisitante = $resultadoVisitante === 'ganado' ? 3 : ($resultadoVisitante === 'empatado' ? 1 : 0);

        // 3) Crear stats base
        $stats = [];
        $torneoId = $this->jornada->torneo_id ?? $this->jornada?->torneo?->id;
        $now = now();

        $jugadoresLocal = $this->equipoLocal ? $this->equipoLocal->jugadoresEnTorneo($torneoId) : collect();
        foreach ($jugadoresLocal as $jugador) {
            $stats[$jugador->id] = [
                'jugador_id' => $jugador->id,
                'partido_id' => $this->id,
                'resultado' => $resultadoLocal,
                'puntos' => $puntosLocal,
                'goles' => 0,
                'asistencias' => 0,
                'tarjetas_amarillas' => 0,
                'tarjetas_rojas' => 0,
                'faltas' => 0,
                'paradas' => 0,
                'created_at' => $now,
                'updated_at' => $now,
            ];
        }

        $jugadoresVisitante = $this->equipoVisitante ? $this->equipoVisitante->jugadoresEnTorneo($torneoId) : collect();
        foreach ($jugadoresVisitante as $jugador) {
            $stats[$jugador->id] = [
                'jugador_id' => $jugador->id,
                'partido_id' => $this->id,
                'resultado' => $resultadoVisitante,
                'puntos' => $puntosVisitante,
                'goles' => 0,
                'asistencias' => 0,
                'tarjetas_amarillas' => 0,
                'tarjetas_rojas' => 0,
                'faltas' => 0,
                'paradas' => 0,
                'created_at' => $now,
                'updated_at' => $now,
            ];
        }

        // 4) Sumar puntos y contadores de eventos
        $eventos = $this->eventos;
        while (is_string($eventos)) {
            $decoded = json_decode($eventos, true);
            if (! is_array($decoded) && ! is_string($decoded)) {
                break;
            }
            $eventos = $decoded;
        }

        if (is_array($eventos)) {
            foreach ($eventos as $evento) {
                $jugadorId = $evento['jugador_id'] ?? null;
                if (! $jugadorId) {
                    continue;
                }

                if (! isset($stats[$jugadorId])) {
                    $stats[$jugadorId] = [
                        'jugador_id' => $jugadorId,
                        'partido_id' => $this->id,
                        'resultado' => 'empatado',
                        'puntos' => 0,
                        'goles' => 0,
                        'asistencias' => 0,
                        'tarjetas_amarillas' => 0,
                        'tarjetas_rojas' => 0,
                        'faltas' => 0,
                        'paradas' => 0,
                        'created_at' => $now,
                        'updated_at' => $now,
                    ];
                }

                switch ($evento['tipo'] ?? '') {
                    case 'Gol':
                        $stats[$jugadorId]['goles'] += 1;
                        $stats[$jugadorId]['puntos'] += 5;
                        break;
                    case 'Asistencia':
                        $stats[$jugadorId]['asistencias'] += 1;
                        $stats[$jugadorId]['puntos'] += 3;
                        break;
                    case 'Tarjeta Amarilla':
                        $stats[$jugadorId]['tarjetas_amarillas'] += 1;
                        $stats[$jugadorId]['puntos'] -= 3;
                        break;
                    case 'Tarjeta Roja':
                        $stats[$jugadorId]['tarjetas_rojas'] += 1;
                        $stats[$jugadorId]['puntos'] -= 5;
                        break;
                    case 'Falta':
                        $stats[$jugadorId]['faltas'] += 1;
                        $stats[$jugadorId]['puntos'] -= 1;
                        break;
                    case 'Parada':
                        $stats[$jugadorId]['paradas'] += 1;
                        $stats[$jugadorId]['puntos'] += 2;
                        break;
                }
            }
        }

        if (! empty($stats)) {
            DB::table('estadisticas')->insert(array_values($stats));
        }
    }
}
