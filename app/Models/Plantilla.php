<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Plantilla extends Model
{
    protected $table = 'plantillas';
    public $timestamps = true;
    protected $fillable = [
        'liguilla_id',
        'cuenta_id',
    ];
    public function usuario()
    {
        return $this->belongsTo(Cuenta::class, 'cuenta_id');
    }

    public function liguilla()
    {
        return $this->belongsTo(Liguilla::class);
    }

    public function jugadores()
    {
        return $this->belongsToMany(Jugador::class, 'jugador_plantilla')
            ->withPivot('posicion')
            ->withTimestamps();
    }
}
