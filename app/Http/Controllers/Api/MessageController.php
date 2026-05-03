<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Message;
use App\Models\UserMatch;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Broadcast;

class MessageController extends Controller
{
    public function index($matchId)
    {
        $userId = Auth::id();

        $match = UserMatch::where('id', $matchId)
            ->where(function ($query) use ($userId) {
                $query->where('user1_id', $userId)
                    ->orWhere('user2_id', $userId);
            })
            ->firstOrFail();

        $messages = Message::where('match_id', $matchId)
            ->with('sender')
            ->orderBy('created_at')
            ->paginate(50);

        Message::where('match_id', $matchId)
            ->where('sender_id', '!=', $userId)
            ->whereNull('read_at')
            ->update(['read_at' => now()]);

        return response()->json($messages);
    }

    public function store(Request $request, $matchId)
    {
        $request->validate(['body' => 'required|string|max:5000']);

        $userId = Auth::id();

        $match = UserMatch::where('id', $matchId)
            ->where(function ($query) use ($userId) {
                $query->where('user1_id', $userId)
                    ->orWhere('user2_id', $userId);
            })
            ->firstOrFail();

        $message = Message::create([
            'match_id' => $matchId,
            'sender_id' => $userId,
            'body' => $request->body,
        ]);

        $message->load('sender');

        try {
            broadcast(new \App\Events\MessageSent($message))->toOthers();
            \Log::info('Broadcasting MessageSent', ['match_id' => $matchId, 'message_id' => $message->id]);
        } catch (\Exception $e) {
            \Log::error('Broadcast error: ' . $e->getMessage());
        }

        return response()->json($message, 201);
    }

    public function markAsRead($matchId)
    {
        $userId = Auth::id();

        Message::where('match_id', $matchId)
            ->where('sender_id', '!=', $userId)
            ->whereNull('read_at')
            ->update(['read_at' => now()]);

        return response()->json(['success' => true]);
    }
}
