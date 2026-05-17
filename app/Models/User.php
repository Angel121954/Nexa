<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Laravel\Fortify\TwoFactorAuthenticatable as FortifyTwoFactor;

class User extends Authenticatable
{
    use HasFactory, Notifiable, FortifyTwoFactor;

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
        'latitude',
        'longitude',
        'last_activity_at',
    ];

    protected $hidden = [
        'password',
        'remember_token',
        'two_factor_secret',
        'two_factor_recovery_codes',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'birth_date' => 'date',
            'looking_for' => 'array',
            'profile_completed' => 'boolean',
            'latitude' => 'float',
            'longitude' => 'float',
            'last_activity_at' => 'datetime',
            'two_factor_confirmed_at' => 'datetime',
        ];
    }

    // 🟢 PERFIL
    public function profile(): HasOne
    {
        return $this->hasOne(Profile::class);
    }

    // 📸 FOTOS
    public function photos(): HasMany
    {
        return $this->hasMany(UserPhoto::class)->orderBy('sort_order');
    }

    // ❤️ INTERESES
    public function interests(): BelongsToMany
    {
        return $this->belongsToMany(Interest::class);
    }

    // 👍 LIKES
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

    //  BLOCKS
    public function blocksSent(): HasMany
    {
        return $this->hasMany(Block::class, 'blocker_id');
    }

    public function blocksReceived(): HasMany
    {
        return $this->hasMany(Block::class, 'blocked_id');
    }

    public function hasBlocked(int $userId): bool
    {
        return $this->blocksSent()
            ->where('blocked_id', $userId)
            ->exists();
    }

    public function isBlockedBy(int $userId): bool
    {
        return $this->blocksReceived()
            ->where('blocker_id', $userId)
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
                return $match->user1_id == $this->id
                    ? $match->user2_id
                    : $match->user1_id;
            });

        return User::whereIn('id', $userIds);
    }

    public function isMatchedWith(int $userId): bool
    {
        return UserMatch::where(function ($query) use ($userId) {
            $query->where('user1_id', $this->id)
                  ->where('user2_id', $userId);
        })->orWhere(function ($query) use ($userId) {
            $query->where('user1_id', $userId)
                  ->where('user2_id', $this->id);
        })->exists();
    }

    // 📝 STORIES
    public function stories(): HasMany
    {
        return $this->hasMany(Story::class);
    }

    public function activeStories(): HasMany
    {
        return $this->hasMany(Story::class)->active();
    }

    public function hasActiveStories(): bool
    {
        return $this->activeStories()->exists();
    }

    // 🔔 NOTIFICACIONES
    public function notifications(): HasMany
    {
        return $this->hasMany(Notification::class)->latest();
    }

    public function unreadNotifications(): HasMany
    {
        return $this->hasMany(Notification::class)->whereNull('read_at');
    }

    // 🖼️ AVATAR (SOLO CLOUDINARY)
    public function getAvatarUrlAttribute()
    {
        return (!empty($this->avatar) && filter_var($this->avatar, FILTER_VALIDATE_URL))
            ? $this->avatar
            : 'https://ui-avatars.com/api/?name=' . urlencode($this->name);
    }
}