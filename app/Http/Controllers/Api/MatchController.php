<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\UserMatch;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class MatchController extends Controller
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

                return [
                    'id' => $match->id,
                    'other_user' => [
                        'id' => $otherUser->id,
                        'name' => $otherUser->name,
                        'avatar' => $otherUser->avatar,
                    ],
                    'last_message' => $lastMessage ? $lastMessage->body : null,
                    'unread_count' => $unreadCount,
                    'created_at' => $match->created_at,
                ];
            });

        return response()->json($matches);
    }

    public function show($id)
    {
        $userId = Auth::id();

        $match = UserMatch::where('id', $id)
            ->where(function ($query) use ($userId) {
                $query->where('user1_id', $userId)
                    ->orWhere('user2_id', $userId);
            })
            ->with(['user1', 'user2'])
            ->firstOrFail();

        $otherUser = $match->user1_id == $userId ? $match->user2 : $match->user1;

        return response()->json([
            'id' => $match->id,
            'other_user' => [
                'id' => $otherUser->id,
                'name' => $otherUser->name,
                'avatar' => $otherUser->avatar,
            ],
        ]);
    }

    public function destroy($id): JsonResponse
    {
        $userId = Auth::id();

        $match = UserMatch::where('id', $id)
            ->where(function ($query) use ($userId) {
                $query->where('user1_id', $userId)
                    ->orWhere('user2_id', $userId);
            })
            ->first();

        if (!$match) {
            return response()->json(['error' => 'Match no encontrado.'], 404);
        }

        if ($match->user1_id === $userId) {
            $match->update(['user1_deleted_at' => now()]);
        } else {
            $match->update(['user2_deleted_at' => now()]);
        }

        return response()->json(['message' => 'Conversación eliminada.']);
    }
}
