<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'username',
        'first_name',
        'last_name',
        'email',
        'password',
        'role',
        'keycloak_id',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Full name accessor
     */
    public function getFullNameAttribute(): string
    {
        return ($this->first_name . ' ' . $this->last_name);
    }

    /**
     * Boot model (NO name column anymore)
     */
    protected static function boot()
    {
        parent::boot();

        static::saving(function ($user) {
            // optional normalization (safe)
            $user->first_name = ucfirst($user->first_name);
            $user->last_name  = ucfirst($user->last_name);
        });
    }

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }
}
