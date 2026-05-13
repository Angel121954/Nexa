<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

class UserMatch extends Model
{
    protected $table = 'matches';

    protected $fillable = ['user1_id', 'user2_id'];

    public function user1(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user1_id');
    }

    public function user2(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user2_id');
    }

    public function messages()
    {
        return $this->hasMany(Message::class, 'match_id');
    }

    /**
     * Trae el mensaje más reciente del match usando latestOfMany(),
     * que funciona correctamente en eager loads (sin el bug de limit(1)).
     */
    public function latestMessage(): HasOne
    {
        return $this->hasOne(Message::class, 'match_id')->latestOfMany();
    }

    public function otherUser($userId)
    {
        return $this->user1_id == $userId ? $this->user2 : $this->user1;
    }
}
