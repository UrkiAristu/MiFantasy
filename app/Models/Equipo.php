<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Stancl\Tenancy\Database\Concerns\BelongsToTenant;

class Equipo extends Model
{
    use BelongsToTenant, HasFactory;

    protected $table = 'equipos';

    public $timestamps = true;

    protected $guarded = [];

    /**
     * @return BelongsToMany<Jugador, $this>
     */
    public function jugadores(): BelongsToMany
    {
        return $this->belongsToMany(Jugador::class, 'equipo_jugador')
            ->withPivot('fecha_union')
            ->withTimestamps();
    }

    /**
     * @return BelongsToMany<Torneo, $this>
     */
    public function torneos(): BelongsToMany
    {
        return $this->belongsToMany(Torneo::class, 'equipo_torneo')
            ->withTimestamps();
    }

    /**
     * @return BelongsToMany<Jugador, $this>
     */
    public function jugadoresEnTorneos(): BelongsToMany
    {
        return $this->belongsToMany(Jugador::class, 'equipo_jugador_torneo')
            ->withPivot(['torneo_id', 'goles', 'asistencias', 'puntos'])
            ->withTimestamps();
    }

    public function jugadoresEnTorneo($torneoId)
    {
        return $this->jugadoresEnTorneos()
            ->wherePivot('torneo_id', $torneoId)
            ->get();
    }

    /**
     * @return HasMany<Partido, $this>
     */
    public function partidosLocal(): HasMany
    {
        return $this->hasMany(Partido::class, 'equipo_local_id');
    }

    /**
     * @return HasMany<Partido, $this>
     */
    public function partidosVisitante(): HasMany
    {
        return $this->hasMany(Partido::class, 'equipo_visitante_id');
    }

    public function partidos()
    {
        return $this->partidosLocal->merge($this->partidosVisitante);
    }
}
