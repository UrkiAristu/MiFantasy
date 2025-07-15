<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Partido extends Model
{
    protected $table = 'partidos';
    public $timestamps = true;
    public function torneo()
    {
        return $this->belongsTo(Torneo::class, 'torneo_id');
    }

    public function equipoLocal()
    {
        return $this->belongsTo(Equipo::class, 'equipo_local_id');
    }

    public function equipoVisitante()
    {
        return $this->belongsTo(Equipo::class, 'equipo_visitante_id');
    }
    public function estadisticas()
    {
        return $this->hasMany(Estadistica::class, 'partido_id');
    }
    public function actualizarEstadisticas()
    {
        //1) Borrar todo para este partido
        Estadistica::where('partido_id', $this->id)->delete();

        // 2) Resultado base
        $localGoles = $this->goles_local;
        $visitanteGoles = $this->goles_visitante;

        if ($localGoles === null || $visitanteGoles === null) return;

        $resultadoLocal = $localGoles > $visitanteGoles ? 'ganado' : ($localGoles < $visitanteGoles ? 'perdido' : 'empatado');
        $resultadoVisitante = $localGoles > $visitanteGoles ? 'perdido' : ($localGoles < $visitanteGoles ? 'ganado' : 'empatado');
        $puntosLocal = $resultadoLocal === 'ganado' ? 3 : ($resultadoLocal === 'empatado' ? 1 : 0);
        $puntosVisitante = $resultadoVisitante === 'ganado' ? 3 : ($resultadoVisitante === 'empatado' ? 1 : 0);

        // 3) Crear stats base (solo resultado y puntos base)
        $stats = collect();

        foreach ($this->equipoLocal->jugadoresEnTorneo($this->torneo->id) as $jugador) {
            $stats->push(
                Estadistica::create([
                    'jugador_id' => $jugador->id,
                    'partido_id' => $this->id,
                    'resultado' => $resultadoLocal,
                    'puntos' => $puntosLocal,
                ])
            );
        }
        foreach ($this->equipoVisitante->jugadoresEnTorneo($this->torneo->id) as $jugador) {
            $stats->push(
                Estadistica::create([
                    'jugador_id' => $jugador->id,
                    'partido_id' => $this->id,
                    'resultado' => $resultadoVisitante,
                    'puntos' => $puntosVisitante,
                ])
            );
        }

        // 4) Sumar puntos y contadores de eventos
        $eventos = json_decode($this->eventos, true);
        if ($eventos) {
            foreach ($eventos as $evento) {
                $stat = Estadistica::where('jugador_id', $evento['jugador_id'])
                    ->where('partido_id', $this->id)
                    ->first();

                if (!$stat) continue;

                switch ($evento['tipo']) {
                    case 'Gol':
                        $stat->goles += 1;
                        $stat->puntos += 5;
                        break;
                    case 'Asistencia':
                        $stat->asistencias += 1;
                        $stat->puntos += 3;
                        break;
                    case 'Tarjeta Amarilla':
                        $stat->tarjetas_amarillas += 1;
                        $stat->puntos -= 3;
                        break;
                    case 'Tarjeta Roja':
                        $stat->tarjetas_rojas += 1;
                        $stat->puntos -= 5;
                        break;
                    case 'Falta':
                        $stat->faltas += 1;
                        $stat->puntos -= 1;
                        break;
                    default:
                        break;
                }

                $stat->save();
            }
        }
    }
}
