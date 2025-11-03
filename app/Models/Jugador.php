<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Jugador extends Model
{
    protected $table = 'jugadores';
    public $timestamps = true;
    public function equipos()
    {
        return $this->belongsToMany(Equipo::class, 'equipo_jugador')
            ->withPivot('fecha_union')
            ->withTimestamps();
    }

    public function participaciones()
    {
        return $this->belongsToMany(Torneo::class, 'equipo_jugador_torneo')
            ->withPivot('equipo_id', 'goles', 'asistencias', 'puntos')
            ->withTimestamps();
    }
    public function equipoEnTorneo($torneoId)
    {
        $participacion = $this->participaciones()->where('torneo_id', $torneoId)->first();;
        if ($participacion) {
            return Equipo::find($participacion->pivot->equipo_id);
        }
        return null;
    }
    public function estadisticas()
    {
        return $this->hasMany(Estadistica::class);
    }

    public function resumenEstadisticasEnTorneo($torneoId)
    {
        // Buscar el torneo con sus jornadas y partidos
        $torneo = Torneo::with('jornadas.partidos')->findOrFail($torneoId);

        // Recoger todos los partidos de todas las jornadas
        $partidos = $torneo->jornadas->flatMap->partidos;

        // Obtener las estadísticas del jugador solo para esos partidos
        $estadisticas = $this->estadisticas()
            ->whereIn('partido_id', $partidos->pluck('id'))
            ->get();

        return [
            'partidos_jugados' => $estadisticas->count(),
            'goles'            => $estadisticas->sum('goles'),
            'asistencias'      => $estadisticas->sum('asistencias'),
            'paradas'          => $estadisticas->sum('paradas'),
            'faltas'           => $estadisticas->sum('faltas'),
            'amarillas'        => $estadisticas->sum('tarjetas_amarillas'),
            'rojas'            => $estadisticas->sum('tarjetas_rojas'),
            'puntos'           => $estadisticas->sum('puntos'),
        ];
    }


    public function plantillas()
    {
        return $this->belongsToMany(Plantilla::class, 'jugador_plantilla')
            ->withPivot('posicion')
            ->withTimestamps();
    }

    public function alineaciones()
    {
        return $this->belongsToMany(Alineacion::class, 'alineacion_jugador')
            ->withPivot('puntos')
            ->withTimestamps();
    }
}
