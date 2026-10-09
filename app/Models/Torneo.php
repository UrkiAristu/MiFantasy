<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Torneo extends Model
{
    use HasFactory;

    protected $table = 'torneos';

    public $timestamps = true;

    protected $fillable = [
        'tenant_id',
        'nombre',
        'fecha_inicio',
        'fecha_fin',
        'descripcion',
        'logo',
        'estado',
        'modalidad',
        'usa_posiciones',
    ];

    protected $casts = [
        'estado' => 'boolean',
        'usa_posiciones' => 'boolean',
    ];

    public function scopeActivos($query)
    {
        return $query->where('estado', true);
    }

    public function getJugadoresPorEquipoAttribute(): int
    {
        return match ((string) ($this->attributes['modalidad'] ?? 'sala')) {
            '11' => 11,
            '7' => 7,
            'sala' => 5,
            default => 5,
        };
    }

    public function setJugadoresPorEquipoAttribute($value): void
    {
        $this->attributes['modalidad'] = match ((int) $value) {
            11 => '11',
            7 => '7',
            default => 'sala',
        };
    }

    public function getFormacionPorDefectoAttribute(): string
    {
        return match ((string) ($this->attributes['modalidad'] ?? 'sala')) {
            '11' => '4-4-2',
            '7' => '3-2-1',
            'sala' => '1-2-1',
            default => '1-2-1',
        };
    }

    /**
     * @return BelongsToMany<Equipo, $this>
     */
    public function equipos(): BelongsToMany
    {
        return $this->belongsToMany(Equipo::class, 'equipo_torneo')
            ->withTimestamps();
    }

    /**
     * @return BelongsToMany<Jugador, $this>
     */
    public function jugadores(): BelongsToMany
    {
        return $this->belongsToMany(Jugador::class, 'equipo_jugador_torneo')
            ->withPivot('equipo_id', 'goles', 'asistencias', 'puntos')
            ->withTimestamps();
    }

    /**
     * @return HasMany<Jornada, $this>
     */
    public function jornadas(): HasMany
    {
        return $this->hasMany(Jornada::class);
    }

    /**
     * @return HasMany<Liguilla, $this>
     */
    public function liguillas(): HasMany
    {
        return $this->hasMany(Liguilla::class);
    }
}
