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

    public function __construct(Notification $notification, int $unreadCount)
    {
        $this->notification = $notification;
        $this->unreadCount = $unreadCount;
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
            'created_at'   => $this->notification->created_at->diffForHumans(),
            'unread_count' => $this->unreadCount,
        ];
    }
}
