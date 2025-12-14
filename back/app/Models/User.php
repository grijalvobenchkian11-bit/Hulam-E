<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * Only fields that are SAFE to mass-assign at registration.
     * Everything else is set explicitly in the controller.
     */
    protected $fillable = [
        'name',
        'email',
        'password',

        // Explicit defaults (OPTION 2)
        'role',
        'verified',
        'profile_completion',
        'verification_status',
    ];

    /**
     * Hidden attributes
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Attribute casting
     * KEEP THIS MINIMAL to avoid DB mismatch crashes
     */
    protected $casts = [
        'verified' => 'boolean',
    ];

    /**
     * Relationships (safe to keep)
     */
    public function rentals()
    {
        return $this->hasMany(Rental::class);
    }

    /**
     * Helpers
     */
    public function isAdmin()
    {
        return $this->role === 'admin';
    }
}
