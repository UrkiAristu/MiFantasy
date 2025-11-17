<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Alineacion extends Model
{
    protected $table = 'alineaciones';
    public $timestamps = true;
    protected $fillable = [
        'user_id',
        'liguilla_id',
        'jornada_id',
    ];
    public function usuario()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function liguilla()
    {
        return $this->belongsTo(Liguilla::class);
    }

    public function jornada()
    {
        return $this->belongsTo(Jornada::class);
    }

    public function jugadores()
    {
        return $this->belongsToMany(Jugador::class, 'alineacion_jugador')
            ->withPivot('puntos')
            ->withTimestamps();
    }
}
