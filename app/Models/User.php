<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Relations\HasMany;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'email',
        'password',
        'google_id',
        'facebook_id',
        'avatar',
        'avatar_public_id',
    ];

    protected $hidden = ['password', 'remember_token'];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password'          => 'hashed',
        ];
    }

    public function profile()
    {
        return $this->hasOne(Profile::class);
    }

    public function photos()
    {
        return $this->hasMany(UserPhoto::class)->orderBy('sort_order');
    }

    public function interests()
    {
        return $this->belongsToMany(Interest::class);
    }

    public function likesSent(): HasMany
    {
        return $this->hasMany(Like::class, 'sender_id');
    }

    public function likesReceived(): HasMany
    {
        return $this->hasMany(Like::class, 'receiver_id');
    }

    public function hasLiked(int $userId): bool
    {
        return $this->likesSent()->where('receiver_id', $userId)->exists();
    }
}
