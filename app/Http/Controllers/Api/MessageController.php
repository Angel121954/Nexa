<?php

namespace App\Http\Controllers\Api;

use App\Events\NotificationCreated;
use App\Http\Controllers\Controller;
use App\Models\Message;
use App\Models\Notification;
use App\Models\User;
use App\Models\UserMatch;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;

class MessageController extends Controller
{
    public function index($matchId)
    {
        $userId = Auth::id();

        UserMatch::where('id', $matchId)
            ->where(function ($query) use ($userId) {
                $query->where('user1_id', $userId)
                    ->orWhere('user2_id', $userId);
            })
            ->firstOrFail();

        $messages = Message::where('match_id', $matchId)
            ->with('sender')
            ->withTrashed()
            ->latest()
            ->paginate(50);

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

        $otherUserId = $match->user1_id == $userId ? $match->user2_id : $match->user1_id;

        if (auth()->user()->hasBlocked($otherUserId)) {
            return response()->json(['error' => 'Has bloqueado a este usuario.'], 403);
        }

        if (auth()->user()->isBlockedBy($otherUserId)) {
            return response()->json(['error' => 'Este usuario te ha bloqueado.'], 403);
        }

        $message = Message::create([
            'match_id'  => $matchId,
            'sender_id' => $userId,
            'body'      => $request->body,
        ]);

        $match->update([
            'user1_deleted_at' => null,
            'user2_deleted_at' => null,
        ]);

        $message->load('sender');

        $unreadCount = Message::whereHas('match', function ($query) use ($otherUserId) {
            $query->where('user1_id', $otherUserId)
                ->orWhere('user2_id', $otherUserId);
        })
            ->where('sender_id', '!=', $otherUserId)
            ->whereNull('read_at')
            ->count();

        try {
            broadcast(new \App\Events\MessageSent($message, $unreadCount))->toOthers();
        } catch (\Exception $e) {
            \Log::error('Broadcast error: ' . $e->getMessage());
        }

        $recipientInChat = false;
        try {
            $pusher = new \Pusher\Pusher(
                config('broadcasting.connections.reverb.key'),
                config('broadcasting.connections.reverb.secret'),
                config('broadcasting.connections.reverb.app_id'),
                config('broadcasting.connections.reverb.options')
            );
            $channelInfo = $pusher->getUsersInPresenceChannel('presence-match.' . $matchId);
            $recipientInChat = collect($channelInfo['users'] ?? [])
                ->contains(fn($u) => (string) $u['id'] === (string) $otherUserId);
        } catch (\Exception $e) {
            \Log::warning('[Nexa] Error al consultar presence channel: ' . $e->getMessage());
        }

        if (!$recipientInChat) {
            $sender = Auth::user();
            $notif = Notification::create([
                'user_id' => $otherUserId,
                'type'    => 'message',
                'data'    => [
                    'actor_name'   => $sender->name,
                    'actor_avatar' => $sender->avatar,
                    'message'      => 'te ha enviado un mensaje.',
                    'preview'      => $message->body,
                    'action_url'   => route('messages.index'),
                ],
            ]);
            $unread = Notification::where('user_id', $otherUserId)->whereNull('read_at')->count();
            $total = Notification::where('user_id', $otherUserId)->count();
            broadcast(new NotificationCreated($notif, $unread, $total))->toOthers();
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

    public function unreadCount()
    {
        $userId = Auth::id();

        $count = Message::whereHas('match', function ($query) use ($userId) {
            $query->where('user1_id', $userId)
                ->orWhere('user2_id', $userId);
        })
            ->where('sender_id', '!=', $userId)
            ->whereNull('read_at')
            ->count();

        return response()->json(['count' => $count]);
    }

    public function update(Request $request, $id)
    {
        $request->validate(['body' => 'required|string|max:5000']);

        $message = Message::findOrFail($id);

        if ($message->sender_id !== Auth::id()) {
            return response()->json(['error' => 'No puedes editar este mensaje.'], 403);
        }

        if (! $message->isEditable()) {
            return response()->json(['error' => 'Ya no puedes editar este mensaje (límite de 30 min).'], 403);
        }

        $message->update([
            'body'       => $request->body,
            'edited_at'  => now(),
        ]);

        $message->load('sender');

        try {
            broadcast(new \App\Events\MessageEdited($message))->toOthers();
        } catch (\Exception $e) {
            \Log::error('Broadcast error: ' . $e->getMessage());
        }

        return response()->json($message);
    }

    public function destroy($id)
    {
        $message = Message::findOrFail($id);

        if ($message->sender_id !== Auth::id()) {
            return response()->json(['error' => 'No puedes eliminar este mensaje.'], 403);
        }

        if (! $message->isDeletable()) {
            return response()->json(['error' => 'Ya no puedes eliminar este mensaje (límite de 60 min).'], 403);
        }

        $message->delete();

        try {
            broadcast(new \App\Events\MessageDeleted($message))->toOthers();
        } catch (\Exception $e) {
            \Log::error('Broadcast error: ' . $e->getMessage());
        }

        return response()->json(['success' => true]);
    }
}
