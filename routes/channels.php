<?php

use App\Broadcasting\MatchChannel;
use Illuminate\Support\Facades\Broadcast;

Broadcast::channel('App.Models.User.{id}', function ($user, $id) {
    return (int) $user->id === (int) $id;
});

Broadcast::channel('user.{id}', function ($user, $id) {
    return (int) $user->id === (int) $id;
});

Broadcast::channel('match.{matchId}', MatchChannel::class);

Broadcast::channel('presence-match.{matchId}', function ($user, $matchId) {
    $match = \App\Models\UserMatch::where('id', $matchId)
        ->where(function ($q) use ($user) {
            $q->where('user1_id', $user->id)->orWhere('user2_id', $user->id);
        })
        ->first();
    if (! $match) return false;
    return [
        'id' => $user->id,
        'name' => $user->name,
        'avatar' => $user->avatar,
    ];
});

// Presencia global: cualquier usuario autenticado puede unirse
Broadcast::channel('presence-global', function ($user) {
    return [
        'id' => $user->id,
        'name' => $user->name,
        'avatar' => $user->avatar,
    ];
});
