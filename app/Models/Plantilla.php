<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Plantilla extends Model
{
    use HasFactory;

    protected $table = 'plantillas';

    public $timestamps = true;

    protected $fillable = [
        'liguilla_id',
        'user_id',
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
     * @return BelongsToMany<Jugador, $this>
     */
    public function jugadores(): BelongsToMany
    {
        return $this->belongsToMany(Jugador::class, 'jugador_plantilla')
            ->withPivot('posicion')
            ->withTimestamps();
    }
}
