<?php

namespace App\Http\Controllers;

use App\Events\NotificationCreated;
use App\Models\Notification;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class NotificationController extends Controller
{
    public function index(): View
    {
        $me = auth()->user();

        $paginator = $me->notifications()
            ->paginate(20);

        $unreadCount = $me->unreadNotifications()->count();
        $totalCount  = $me->notifications()->count();

        $notifications = $paginator->groupBy(function ($n) {
            $diff = now()->diffInDays($n->created_at);
            return match (true) {
                $diff === 0 => 'Hoy',
                $diff === 1 => 'Ayer',
                $diff <= 7  => 'Esta semana',
                default     => 'Anteriormente',
            };
        });

        return view('notifications.index', compact('notifications', 'unreadCount', 'totalCount', 'paginator'));
    }

    public function unreadCount(): JsonResponse
    {
        $count = auth()->user()->unreadNotifications()->count();

        return response()->json(['count' => $count]);
    }

    public function read(Notification $notification): JsonResponse
    {
        if ($notification->user_id !== auth()->id()) {
            return response()->json(['error' => 'No autorizado.'], 403);
        }

        $notification->update(['read_at' => now()]);

        return response()->json(['message' => 'Marcada como leída.']);
    }

    public function readAll(): JsonResponse
    {
        auth()->user()->unreadNotifications()->update(['read_at' => now()]);

        return response()->json(['message' => 'Todas marcadas como leídas.']);
    }

    public function preferences(Request $request): JsonResponse
    {
        $request->validate([
            'notify_match'        => 'nullable|boolean',
            'notify_like'         => 'nullable|boolean',
            'notify_message'      => 'nullable|boolean',
            'notify_profile_view' => 'nullable|boolean',
        ]);

        return response()->json(['message' => 'Preferencias guardadas.']);
    }
}
