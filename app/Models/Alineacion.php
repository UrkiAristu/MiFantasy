<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Alineacion extends Model
{
    use HasFactory;

    protected $table = 'alineaciones';

    public $timestamps = true;

    protected $fillable = [
        'user_id',
        'liguilla_id',
        'jornada_id',
        'formacion',
    ];

    /**
     * @return BelongsTo<User, $this>
     */
    public function usuario(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * @return BelongsTo<Liguilla, $this>
     */
    public function liguilla(): BelongsTo
    {
        return $this->belongsTo(Liguilla::class);
    }

    /**
     * @return BelongsTo<Jornada, $this>
     */
    public function jornada(): BelongsTo
    {
        return $this->belongsTo(Jornada::class);
    }

    /**
     * @return BelongsToMany<Jugador, $this>
     */
    public function jugadores(): BelongsToMany
    {
        return $this->belongsToMany(Jugador::class, 'alineacion_jugador')
            ->withPivot('puntos')
            ->withTimestamps();
    }

    public function scopeBase($q)
    {
        return $q->whereNull('jornada_id');
    }

    public function scopeDeJornada($q, $jornadaId)
    {
        return $q->where('jornada_id', $jornadaId);
    }
}
