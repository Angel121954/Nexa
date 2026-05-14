<?php

namespace App\Events;

use App\Models\Notification;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class NotificationCreated implements ShouldBroadcastNow
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public Notification $notification;
    public int $unreadCount;
    public int $totalCount;

    public function __construct(Notification $notification, int $unreadCount, int $totalCount)
    {
        $this->notification = $notification;
        $this->unreadCount = $unreadCount;
        $this->totalCount = $totalCount;
    }

    public function broadcastAs(): string
    {
        return 'NotificationCreated';
    }

    public function broadcastOn(): array
    {
        return [
            new PrivateChannel('user.' . $this->notification->user_id),
        ];
    }

    public function broadcastWith(): array
    {
        $data = $this->notification->data;

        return [
            'id'           => $this->notification->id,
            'type'         => $this->notification->type,
            'data'         => $data,
            'timestamp'    => $this->notification->created_at->timestamp,
            'unread_count' => $this->unreadCount,
            'total_count'  => $this->totalCount,
        ];
    }
}
