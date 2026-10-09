<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * @property int|string $posicion_usuario
 * @property int|float $puntos_usuario
 */
class Liguilla extends Model
{
    use HasFactory;

    protected $table = 'liguillas';

    public $timestamps = true;

    protected $guarded = [];

    /**
     * @return BelongsTo<Torneo, $this>
     */
    public function torneo(): BelongsTo
    {
        return $this->belongsTo(Torneo::class);
    }

    /**
     * @return BelongsTo<User, $this>
     */
    public function creador(): BelongsTo
    {
        return $this->belongsTo(User::class, 'creador_id');
    }

    /**
     * @return BelongsToMany<User, $this>
     */
    public function usuarios(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'liguilla_usuario', 'liguilla_id', 'user_id')
            ->withPivot('puesto', 'puntos')
            ->withTimestamps();
    }

    /**
     * @return HasMany<Alineacion, $this>
     */
    public function alineaciones(): HasMany
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

    /**
     * @return HasMany<Plantilla, $this>
     */
    public function plantillas(): HasMany
    {
        return $this->hasMany(Plantilla::class);
    }
}
