<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Equipo extends Model
{
    protected $table = 'equipos';
    public $timestamps = true;
    public function jugadores()
    {
        return $this->belongsToMany(Jugador::class, 'equipo_jugador')
            ->withPivot('fecha_union')
            ->withTimestamps();
    }

    public function torneos()
    {
        return $this->belongsToMany(Torneo::class, 'equipo_torneo')
            ->withTimestamps();
    }

    public function jugadoresEnTorneos()
    {
        return $this->belongsToMany(Jugador::class, 'equipo_jugador_torneo')
            ->withPivot(['torneo_id', 'goles', 'asistencias', 'puntos'])
            ->withTimestamps();
    }
    public function jugadoresEnTorneo($torneoId)
    {
        return $this->jugadoresEnTorneos()
            ->wherePivot('torneo_id', $torneoId)
            ->get();
    }

    public function partidosLocal()
    {
        return $this->hasMany(Partido::class, 'equipo_local_id');
    }

    public function partidosVisitante()
    {
        return $this->hasMany(Partido::class, 'equipo_visitante_id');
    }
    public function partidos()
    {
        return $this->partidosLocal->merge($this->partidosVisitante);
    }
}
