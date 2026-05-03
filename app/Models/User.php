<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

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
        'birth_date',
        'gender',
        'pronouns',
        'looking_for',
        'bio',
        'city',
        'profile_completed',
    ];

    protected $hidden = ['password', 'remember_token'];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'birth_date' => 'date',
            'looking_for' => 'array',
            'profile_completed' => 'boolean',
        ];
    }

    //  PERFIL (CLAVE PARA TU VISTA)
    public function profile(): HasOne
    {
        return $this->hasOne(Profile::class);
    }

    //  FOTOS
    public function photos(): HasMany
    {
        return $this->hasMany(UserPhoto::class)->orderBy('sort_order');
    }

    //  INTERESES
    public function interests(): BelongsToMany
    {
        return $this->belongsToMany(Interest::class);
    }

    //  LIKES
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
        return $this->likesSent()
            ->where('receiver_id', $userId)
            ->exists();
    }

    //  MATCHES
    public function matches()
    {
        return UserMatch::where('user1_id', $this->id)
            ->orWhere('user2_id', $this->id);
    }

    public function matchedUsers()
    {
        $matchIds = $this->matches()->pluck('id')->toArray();

        $userIds = UserMatch::whereIn('id', $matchIds)
            ->get()
            ->map(function ($match) {
                return $match->user1_id == $this->id ? $match->user2_id : $match->user1_id;
            });

        return User::whereIn('id', $userIds);
    }

    public function isMatchedWith(int $userId): bool
    {
        return UserMatch::where(function ($query) use ($userId) {
            $query->where('user1_id', $this->id)->where('user2_id', $userId);
        })->orWhere(function ($query) use ($userId) {
            $query->where('user1_id', $userId)->where('user2_id', $this->id);
        })->exists();
    }
}
