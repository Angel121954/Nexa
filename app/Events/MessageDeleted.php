<?php

namespace App\Events;

use App\Models\Message;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class MessageDeleted implements ShouldBroadcastNow
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $message;
    public $recipientId;

    public function __construct(Message $message)
    {
        $this->message = $message;
        $match = $message->match;
        $this->recipientId = $match->user1_id == $message->sender_id
            ? $match->user2_id
            : $match->user1_id;
    }

    public function broadcastOn(): array
    {
        return [
            new PrivateChannel('match.' . $this->message->match_id),
            new PrivateChannel('user.' . $this->recipientId),
        ];
    }

    public function broadcastAs(): string
    {
        return 'MessageDeleted';
    }

    public function broadcastWith(): array
    {
        return [
            'id'        => $this->message->id,
            'match_id'  => $this->message->match_id,
            'sender_id' => $this->message->sender_id,
        ];
    }
}
