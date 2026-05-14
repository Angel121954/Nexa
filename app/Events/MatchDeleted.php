<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class MatchDeleted implements ShouldBroadcastNow
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public function __construct(
        public int $matchId,
        public int $deletedByUserId
    ) {}

    public function broadcastOn(): array
    {
        return [
            new Channel('matches'),
        ];
    }

    public function broadcastWith(): array
    {
        return [
            'match_id'         => $this->matchId,
            'deleted_by_user_id' => $this->deletedByUserId,
        ];
    }
}
