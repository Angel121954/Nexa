<?php

namespace App\Events;

use App\Models\Story;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class StoryCreated implements ShouldBroadcastNow
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public Story $story;

    public function __construct(Story $story)
    {
        $this->story = $story->load('user:id,name,avatar');
    }

    public function broadcastOn(): array
    {
        return [
            new Channel('stories'),
        ];
    }

    public function broadcastWith(): array
    {
        return [
            'story' => [
                'id'        => $this->story->id,
                'user_id'   => $this->story->user_id,
                'media_url' => $this->story->media_path,
                'created_at' => $this->story->created_at->diffForHumans(),
            ],
            'user' => [
                'id'     => $this->story->user->id,
                'name'   => $this->story->user->name,
                'avatar' => $this->story->user->avatar_url,
            ],
        ];
    }
}
