<?php

namespace App\Models;

use App\Notifications\ResetearPassword;
use App\Notifications\VerificarEmail;
use Database\Factories\UserFactory;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Cashier\Billable;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable implements MustVerifyEmail
{
    /** @use HasFactory<UserFactory> */
    use Billable, HasFactory, HasRoles, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'active',
        'admin',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'active' => 'boolean',
            'admin' => 'boolean',
        ];
    }

    public function sendEmailVerificationNotification()
    {
        $this->notify(new VerificarEmail);
    }

    public function sendPasswordResetNotification($token)
    {
        $this->notify(new ResetearPassword($token));
    }

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
            ->withPivot('puesto', 'puntos')
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
