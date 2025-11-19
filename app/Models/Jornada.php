<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Jornada extends Model
{
    protected $table = 'jornadas';
    public $timestamps = true;

    protected $casts = [
        'fecha_inicio'              => 'date',
        'fecha_fin'                 => 'date',
        'fecha_cierre_alineaciones' => 'datetime',
        'alineaciones_congeladas'   => 'boolean',
    ];

    public function torneo()
    {
        return $this->belongsTo(Torneo::class);
    }

    public function partidos()
    {
        return $this->hasMany(Partido::class);
    }
    public function alineaciones()
    {
        return $this->hasMany(Alineacion::class);
    }
}
