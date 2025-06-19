<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Torneo extends Model
{
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
}
