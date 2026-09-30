<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Jornada extends Model
{
    use HasFactory;

    protected $table = 'jornadas';

    public $timestamps = true;

    protected $casts = [
        'fecha_inicio' => 'date',
        'fecha_fin' => 'date',
        'fecha_cierre_alineaciones' => 'datetime',
        'alineaciones_congeladas' => 'boolean',
    ];

    /**
     * @return BelongsTo<Torneo, $this>
     */
    public function torneo(): BelongsTo
    {
        return $this->belongsTo(Torneo::class);
    }

    /**
     * @return HasMany<Partido, $this>
     */
    public function partidos(): HasMany
    {
        return $this->hasMany(Partido::class);
    }

    /**
     * @return HasMany<Alineacion, $this>
     */
    public function alineaciones(): HasMany
    {
        return $this->hasMany(Alineacion::class);
    }
}
