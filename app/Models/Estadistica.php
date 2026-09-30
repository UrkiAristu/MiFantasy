<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Estadistica extends Model
{
    use HasFactory;

    protected $fillable = [
        'jugador_id',
        'partido_id',
        'posicion',
        'minutos',
        'goles',
        'asistencias',
        'tarjetas_amarillas',
        'tarjetas_rojas',
        'puntos',
        'resultado',
    ];

    protected $table = 'estadisticas';

    public $timestamps = true;

    /**
     * @return BelongsTo<Jugador, $this>
     */
    public function jugador(): BelongsTo
    {
        return $this->belongsTo(Jugador::class);
    }

    /**
     * @return BelongsTo<Partido, $this>
     */
    public function partido(): BelongsTo
    {
        return $this->belongsTo(Partido::class);
    }
}
