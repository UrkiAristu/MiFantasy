<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Cuenta extends Model
{
    protected $table = 'cuentas';
    public $timestamps = true;

    public function liguillasCreadas()
    {
        return $this->hasMany(Liguilla::class, 'creador_id');
    }
    public function liguillas()
    {
        return $this->belongsToMany(Liguilla::class, 'liguilla_usuario',  'usuario_id', 'liguilla_id')
            ->withTimestamps();
    }
}
