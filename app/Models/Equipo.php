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
}
