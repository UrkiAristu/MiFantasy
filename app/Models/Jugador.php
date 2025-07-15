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

    public function estadisticas()
    {
        return $this->hasMany(Estadistica::class, 'jugador_id');
    }
}
