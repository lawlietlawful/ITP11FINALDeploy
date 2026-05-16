<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'name', 'email', 'password', 'role', 'profile_photo_path',
        'profile_photo_data', 'profile_photo_mime',
    ];

    protected $hidden = [
        'password', 'remember_token', 'profile_photo_data',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    public function getProfilePhotoUrlAttribute()
    {
        // 1. Try filesystem first (works on local dev / fresh deploy)
        if ($this->profile_photo_path && \Illuminate\Support\Facades\Storage::disk('public')->exists($this->profile_photo_path)) {
            return asset('storage/' . $this->profile_photo_path);
        }

        // 2. Fall back to database-stored photo (survives Render deploys)
        if ($this->profile_photo_data && $this->profile_photo_mime) {
            return route('profile.photo.serve');
        }

        // 3. No photo available
        return null;
    }

    public function scopeAdmin($query)
    {
        return $query->where('role', 'admin');
    }

    public function isAdmin(): bool
    {
        return $this->role === 'admin';
    }
}
