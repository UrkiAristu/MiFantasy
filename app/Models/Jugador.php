<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Jugador extends Model
{
    use HasFactory;

    protected $table = 'jugadores';
    public $timestamps = true;

    protected array $equiposTorneoMemo = [];

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
        if (isset($this->equiposTorneoMemo[$torneoId])) {
            return $this->equiposTorneoMemo[$torneoId];
        }

        if ($this->relationLoaded('participaciones')) {
            $participacion = $this->participaciones->firstWhere('id', $torneoId);
            if ($participacion && isset($participacion->pivot->equipo_id)) {
                $equipo = Equipo::find($participacion->pivot->equipo_id);
                return $this->equiposTorneoMemo[$torneoId] = $equipo;
            }
        }

        $equipo = Equipo::join('equipo_jugador_torneo', 'equipos.id', '=', 'equipo_jugador_torneo.equipo_id')
            ->where('equipo_jugador_torneo.jugador_id', $this->id)
            ->where('equipo_jugador_torneo.torneo_id', $torneoId)
            ->select('equipos.*')
            ->first();

        return $this->equiposTorneoMemo[$torneoId] = $equipo;
    }

    public function estadisticas()
    {
        return $this->hasMany(Estadistica::class);
    }

    public function resumenEstadisticasEnTorneo($torneoId)
    {
        $res = DB::table('estadisticas as e')
            ->join('partidos as p', 'p.id', '=', 'e.partido_id')
            ->join('jornadas as j', 'j.id', '=', 'p.jornada_id')
            ->where('j.torneo_id', $torneoId)
            ->where('e.jugador_id', $this->id)
            ->selectRaw('
                COUNT(e.id) as partidos_jugados,
                COALESCE(SUM(e.goles), 0) as goles,
                COALESCE(SUM(e.asistencias), 0) as asistencias,
                COALESCE(SUM(e.paradas), 0) as paradas,
                COALESCE(SUM(e.faltas), 0) as faltas,
                COALESCE(SUM(e.tarjetas_amarillas), 0) as amarillas,
                COALESCE(SUM(e.tarjetas_rojas), 0) as rojas,
                COALESCE(SUM(e.puntos), 0) as puntos
            ')
            ->first();

        return [
            'partidos_jugados' => (int) ($res->partidos_jugados ?? 0),
            'goles'            => (int) ($res->goles ?? 0),
            'asistencias'      => (int) ($res->asistencias ?? 0),
            'paradas'          => (int) ($res->paradas ?? 0),
            'faltas'           => (int) ($res->faltas ?? 0),
            'amarillas'        => (int) ($res->amarillas ?? 0),
            'rojas'            => (int) ($res->rojas ?? 0),
            'puntos'           => (int) ($res->puntos ?? 0),
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
