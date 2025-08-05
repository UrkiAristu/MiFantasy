<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Liguilla extends Model
{
    protected $table = 'liguillas';
    public $timestamps = true;
    public function torneo()
    {
        return $this->belongsTo(Torneo::class,);
    }
    public function creador()
    {
        return $this->belongsTo(Cuenta::class, 'creador_id');
    }
    public function usuarios()
    {
        return $this->belongsToMany(Cuenta::class, 'liguilla_usuario', 'liguilla_id', 'usuario_id')
            ->withPivot('puesto', 'puntos')
            ->withTimestamps()
            ->with('torneo');;
    }
}
