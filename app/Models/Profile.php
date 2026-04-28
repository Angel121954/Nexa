<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Model;

class Profile extends Model
{
    protected $fillable = [
        'bio',
        'city',
        'birth_date',
        'gender',
        'pronouns',
        'looking_for',
        'profile_completed',
        'onboarding_step',
    ];

    protected $casts = [
        'birth_date' => 'date',
        'looking_for' => 'array',
        'profile_completed' => 'boolean',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function getAgeAttribute(): ?int
    {
        return $this->birth_date?->age;
    }
}
