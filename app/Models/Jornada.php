<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Jornada extends Model
{
    protected $table = 'jornadas';
    public $timestamps = true;

    public function torneo()
    {
        return $this->belongsTo(Torneo::class);
    }

    public function partidos()
    {
        return $this->hasMany(Partido::class);
    }
}
