<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Torneo extends Model
{
    protected $table = 'torneos';
    public $timestamps = true;
    public function equipos()
    {
        return $this->belongsToMany(Equipo::class, 'equipo_torneo')
            ->withTimestamps();
    }

    public function jugadores()
    {
        return $this->belongsToMany(Jugador::class, 'equipo_jugador_torneo')
            ->withPivot('equipo_id', 'goles', 'asistencias', 'puntos')
            ->withTimestamps();
    }
    public function jornadas()
    {
        return $this->hasMany(Jornada::class);
    }

    public function liguillas()
    {
        return $this->hasMany(Liguilla::class);
    }
}
