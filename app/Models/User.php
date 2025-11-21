<?php

namespace App\Models;

use App\Notifications\ResetearPassword;
use App\Notifications\VerificarEmail;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable implements MustVerifyEmail
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

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
        $this->notify(new VerificarEmail());
    }
    public function sendPasswordResetNotification($token)
    {
        $this->notify(new ResetearPassword($token));
    }

    public function liguillasCreadas()
    {
        return $this->hasMany(Liguilla::class, 'creador_id');
    }
    public function liguillas()
    {
        return $this->belongsToMany(Liguilla::class, 'liguilla_usuario',  'user_id', 'liguilla_id')
            ->withTimestamps();
    }
    public function plantillaLiguilla($liguillaId)
    {
        return $this->hasMany(Plantilla::class, 'user_id')->where('liguilla_id', $liguillaId);
    }
}
