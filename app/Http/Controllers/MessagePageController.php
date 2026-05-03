<?php

namespace App\Http\Controllers;

use App\Models\Message;
use App\Models\UserMatch;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class MessagePageController extends Controller
{
    public function index()
    {
        $userId = Auth::id();

        $matches = UserMatch::where('user1_id', $userId)
            ->orWhere('user2_id', $userId)
            ->with(['user1', 'user2', 'messages' => function ($query) {
                $query->latest()->limit(1);
            }])
            ->get()
            ->map(function ($match) use ($userId) {
                $otherUser = $match->user1_id == $userId ? $match->user2 : $match->user1;
                $lastMessage = $match->messages->first();

                $unreadCount = Message::where('match_id', $match->id)
                    ->where('sender_id', '!=', $userId)
                    ->whereNull('read_at')
                    ->count();

                return (object) [
                    'id' => $match->id,
                    'otherUser' => (object) [
                        'id' => $otherUser->id,
                        'name' => $otherUser->name,
                        'avatar' => $otherUser->avatar,
                        'is_online' => false,
                    ],
                    'lastMessage' => $lastMessage ? (object) [
                        'body' => $lastMessage->body,
                        'created_at' => $lastMessage->created_at,
                    ] : null,
                    'unread_count' => $unreadCount,
                    'is_match' => true,
                ];
            });

        return view('messages.index', ['conversations' => $matches]);
    }
}
