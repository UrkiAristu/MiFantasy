<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Stancl\Tenancy\Database\Concerns\BelongsToTenant;

class Torneo extends Model
{
    use HasFactory, BelongsToTenant;

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
    public function equipos()
    {
        return $this->belongsToMany(Equipo::class, 'equipo_torneo')
            ->withTimestamps();
    }

    public function jugadores()
    {
        return $this->belongsToMany(Jugador::class, 'equipo_jugador_torneo')
            ->withPivot('equipo_id', 'goles', 'asistencias', 'puntos')
            ->withTimestamps();
    }
    public function jornadas()
    {
        return $this->hasMany(Jornada::class);
    }

    public function liguillas()
    {
        return $this->hasMany(Liguilla::class);
    }
}
