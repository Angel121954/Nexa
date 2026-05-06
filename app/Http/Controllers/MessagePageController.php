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

        // Get all matches
        $matches = UserMatch::where('user1_id', $userId)
            ->orWhere('user2_id', $userId)
            ->with(['user1', 'user2', 'messages' => function ($query) {
                $query->latest()->limit(1);
            }])
            ->get();

        // Mark the first conversation as read (since it will be shown as active)
        $firstMatchId = null;
        $firstMatch = $matches->first();
        if ($firstMatch) {
            $firstMatchId = $firstMatch->id;
            Message::where('match_id', $firstMatch->id)
                ->where('sender_id', '!=', $userId)
                ->whereNull('read_at')
                ->update(['read_at' => now()]);
        }

        $conversations = $matches->map(function ($match) use ($userId, $firstMatchId) {
            $otherUser = $match->user1_id == $userId ? $match->user2 : $match->user1;
            $lastMessage = $match->messages->first();

            // Don't count unread for the first conversation (it's being viewed)
            $unreadCount = ($firstMatchId && $match->id === $firstMatchId) ? 0 : Message::where('match_id', $match->id)
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

        return view('messages.index', ['conversations' => $conversations]);
    }
}
