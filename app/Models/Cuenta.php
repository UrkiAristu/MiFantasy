<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Cuenta extends Model
{
    protected $table = 'cuentas';

    public $timestamps = true;

    /**
     * @return HasMany<Liguilla, $this>
     */
    public function liguillasCreadas(): HasMany
    {
        return $this->hasMany(Liguilla::class, 'creador_id');
    }

    /**
     * @return BelongsToMany<Liguilla, $this>
     */
    public function liguillas(): BelongsToMany
    {
        return $this->belongsToMany(Liguilla::class, 'liguilla_usuario', 'user_id', 'liguilla_id')
            ->withTimestamps();
    }

    /**
     * @param  int  $liguillaId
     * @return HasMany<Plantilla, $this>
     */
    public function plantillaLiguilla($liguillaId): HasMany
    {
        return $this->hasMany(Plantilla::class, 'user_id')->where('liguilla_id', $liguillaId);
    }
}
