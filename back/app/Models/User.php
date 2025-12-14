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
     * IMPORTANT:
     * Disable timestamps if your users table does NOT have
     * created_at / updated_at columns.
     */
    public $timestamps = false;

    /**
     * ONLY fields that are guaranteed to exist in DB
     * and are used during registration / auth.
     */
    protected $fillable = [
        'name',
        'email',
        'password',
    ];

    /**
     * Hidden attributes
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Safe casts ONLY
     */
    protected function casts(): array
    {
        return [
            'password' => 'hashed',
        ];
    }

    /* =========================================================
     |  OPTIONAL RELATIONS & HELPERS (SAFE)
     ========================================================= */

    public function rentals()
    {
        return $this->hasMany(Rental::class);
    }

    public function isAdmin()
    {
        return isset($this->role) && $this->role === 'admin';
    }

    public function getAverageRating()
    {
        return isset($this->total_ratings) && $this->total_ratings > 0
            ? $this->rating
            : 0;
    }

    public function setOnline($online = true)
    {
        if (property_exists($this, 'is_online')) {
            $this->update([
                'is_online' => $online,
                'last_seen' => now(),
            ]);
        }
    }

    public function hasCompleteProfile()
    {
        return isset($this->profile_completion)
            && $this->profile_completion >= 80;
    }

    /**
     * Profile picture helpers (SAFE)
     */
    public function getProfilePictureUrlAttribute()
    {
        if (empty($this->profile_picture)) {
            return null;
        }

        if (str_starts_with($this->profile_picture, 'http')) {
            return $this->profile_picture;
        }

        if (str_starts_with($this->profile_picture, 'data:image/')) {
            return $this->profile_picture;
        }

        return config('app.url') . '/' . ltrim($this->profile_picture, '/');
    }

    protected $appends = [
        'profile_picture_url',
    ];

    public function toArray()
    {
        $array = parent::toArray();
        $array['profile_picture_url'] = $this->profile_picture_url;
        return $array;
    }
}
