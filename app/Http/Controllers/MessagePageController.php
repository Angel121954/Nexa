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

        $matches = UserMatch::where(function ($q) use ($userId) {
                $q->where('user1_id', $userId)->orWhere('user2_id', $userId);
            })
            ->where(function ($q) use ($userId) {
                $q->where('user1_id', $userId)->whereNull('user1_deleted_at')
                  ->orWhere('user2_id', $userId)->whereNull('user2_deleted_at');
            })
            ->with(['user1', 'user2', 'latestMessage'])
            ->get();

        $conversations = $matches
            ->map(function ($match) use ($userId) {
                $otherUser   = $match->user1_id == $userId ? $match->user2 : $match->user1;
                $lastMessage = $match->latestMessage;

                $unreadCount = Message::where('match_id', $match->id)
                    ->where('sender_id', '!=', $userId)
                    ->whereNull('read_at')
                    ->count();

                $isBlocked   = auth()->user()->hasBlocked($otherUser->id);
                $isBlockedBy = auth()->user()->isBlockedBy($otherUser->id);

                return (object) [
                    'id'        => $match->id,
                    'otherUser' => (object) [
                        'id'        => $otherUser->id,
                        'name'      => $otherUser->name,
                        'avatar'    => $otherUser->avatar,
                        'is_online' => false,
                    ],
                    'lastMessage'  => $lastMessage ? (object) [
                        'body'       => $lastMessage->body,
                        'created_at' => $lastMessage->created_at,
                    ] : null,
                    'unread_count' => $unreadCount,
                    'is_match'     => true,
                    'is_blocked'   => $isBlocked,
                    'is_blocked_by' => $isBlockedBy,
                ];
            })
            // Conversaciones ordenadas por actividad más reciente primero
            ->sortByDesc(fn ($c) => $c->lastMessage?->created_at)
            ->values();

        return view('messages.index', ['conversations' => $conversations]);
    }
}
