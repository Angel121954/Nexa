<?php

namespace App\Events;

use App\Models\User;
use App\Models\UserMatch;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class MatchCreated implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $match;
    public $user1;
    public $user2;

    public function __construct(UserMatch $match)
    {
        $this->match = $match;
        $this->user1 = User::find($match->user1_id);
        $this->user2 = User::find($match->user2_id);
    }

    public function broadcastOn(): array
    {
        return [
            new PrivateChannel('user.' . $this->match->user1_id),
            new PrivateChannel('user.' . $this->match->user2_id),
        ];
    }

    public function broadcastWith(): array
    {
        $otherUser = $this->user1->id === auth()->id() ? $this->user2 : $this->user1;

        return [
            'match_id' => $this->match->id,
            'other_user' => [
                'id' => $otherUser->id,
                'name' => $otherUser->name,
                'avatar' => $otherUser->avatar,
            ],
            'created_at' => $this->match->created_at->toIso8601String(),
        ];
    }
}
