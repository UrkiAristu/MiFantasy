<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Stancl\Tenancy\Database\Concerns\BelongsToTenant;

class Liguilla extends Model
{
    use HasFactory, BelongsToTenant;

    protected $table = 'liguillas';
    public $timestamps = true;
    protected $guarded = [];
    public function torneo()
    {
        return $this->belongsTo(Torneo::class,);
    }
    public function creador()
    {
        return $this->belongsTo(User::class, 'creador_id');
    }
    public function usuarios()
    {
        return $this->belongsToMany(User::class, 'liguilla_usuario', 'liguilla_id', 'user_id')
            ->withPivot('puesto', 'puntos')
            ->withTimestamps();
    }
    public function alineaciones()
    {
        return $this->hasMany(Alineacion::class);
    }

    public function alineacionBaseDe(User $user)
    {
        return $this->alineaciones()
                    ->where('user_id', $user->id)
                    ->whereNull('jornada_id')
                    ->with('jugadores')
                    ->first();
    }

    public function plantillas()
    {
        return $this->hasMany(Plantilla::class);
    }
}
