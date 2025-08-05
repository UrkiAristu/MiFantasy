<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Estadistica extends Model
{
    protected $fillable = [
        'jugador_id',
        'partido_id',
        'posicion',
        'minutos',
        'goles',
        'asistencias',
        'tarjetas_amarillas',
        'tarjetas_rojas',
        'puntos',
        'resultado'
    ];
    protected $table = 'estadisticas';
    public $timestamps = true;
    public function jugador()
    {
        return $this->belongsTo(Jugador::class);
    }
    public function partido()
    {
        return $this->belongsTo(Partido::class);
    }
}
