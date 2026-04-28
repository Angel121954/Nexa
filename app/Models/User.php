<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

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
        'bio',
        'city',
        'looking_for',
        'profile_completed',
        'onboarding_step',
    ];

    protected $hidden = ['password', 'remember_token'];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'birth_date'        => 'date',
            'password'          => 'hashed',
            'profile_completed' => 'boolean',
        ];
    }

    public function getAgeAttribute(): ?int
    {
        return $this->birth_date?->age;
    }

    public function photos()
    {
        return $this->hasMany(UserPhoto::class)->orderBy('sort_order');
    }

    public function interests()
    {
        return $this->belongsToMany(Interest::class);
    }
}
