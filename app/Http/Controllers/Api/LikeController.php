<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Like;
use App\Models\UserMatch;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LikeController extends Controller
{
    public function store(Request $request)
    {
        $request->validate(['receiver_id' => 'required|exists:users,id']);

        $senderId = Auth::id();
        $receiverId = $request->receiver_id;

        if ($senderId == $receiverId) {
            return response()->json(['message' => 'Cannot like yourself'], 400);
        }

        $existingLike = Like::where('sender_id', $senderId)
            ->where('receiver_id', $receiverId)
            ->first();

        if ($existingLike) {
            return response()->json(['message' => 'Already liked'], 400);
        }

        Like::create([
            'sender_id' => $senderId,
            'receiver_id' => $receiverId,
        ]);

        $mutualLike = Like::where('sender_id', $receiverId)
            ->where('receiver_id', $senderId)
            ->first();

        if ($mutualLike) {
            $match = UserMatch::create([
                'user1_id' => min($senderId, $receiverId),
                'user2_id' => max($senderId, $receiverId),
            ]);

            return response()->json(['message' => 'Match created!', 'match' => $match], 201);
        }

        return response()->json(['message' => 'Like sent'], 201);
    }

    public function destroy($receiverId)
    {
        $deleted = Like::where('sender_id', Auth::id())
            ->where('receiver_id', $receiverId)
            ->delete();

        if (!$deleted) {
            return response()->json(['message' => 'Like not found'], 404);
        }

        return response()->json(['message' => 'Like removed']);
    }
}
