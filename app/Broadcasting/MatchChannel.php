<?php

namespace App\Broadcasting;

use App\Models\User;
use App\Models\UserMatch;

class MatchChannel
{
    public function join(User $user, $matchId): bool
    {
        return UserMatch::where('id', $matchId)
            ->where(function ($query) use ($user) {
                $query->where('user1_id', $user->id)
                    ->orWhere('user2_id', $user->id);
            })
            ->exists();
    }
}
