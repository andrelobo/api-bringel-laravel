<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, Notifiable;

    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    public const ROLE_ADMIN = 'admin';
    public const ROLE_USER = 'user';

    public function hasRole(string $role): bool
    {
        return $this->role === $role;
    }

    public function assignRole(string $role): void
    {
        $this->update(['role' => $role]);
    }
}

