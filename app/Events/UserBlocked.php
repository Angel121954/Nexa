<?php

namespace App\Events;

use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class UserBlocked implements ShouldBroadcastNow
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public int $blockedUserId;
    public bool $isBlocked;
    public int $blockerId;

    public function __construct(int $blockedUserId, bool $isBlocked, int $blockerId)
    {
        $this->blockedUserId = $blockedUserId;
        $this->isBlocked = $isBlocked;
        $this->blockerId = $blockerId;
    }

    public function broadcastOn(): array
    {
        return [
            new PrivateChannel('user.' . $this->blockedUserId),
        ];
    }

    public function broadcastAs(): string
    {
        return 'UserBlocked';
    }

    public function broadcastWith(): array
    {
        return [
            'is_blocked'  => $this->isBlocked,
            'blocker_id'  => $this->blockerId,
        ];
    }
}
