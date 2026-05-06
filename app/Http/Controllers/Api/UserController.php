<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class UserController extends Controller
{
    /**
     * Devuelve el estado online de los usuarios basado en last_activity_at.
     * Considera "en línea" si la última actividad fue hace menos de 3 minutos.
     */
    public function onlineStatus(Request $request)
    {
        $userIds = $request->input('ids', []);
        $query = User::query();

        if (!empty($userIds)) {
            $query->whereIn('id', $userIds);
        }

        $users = $query->get(['id', 'last_activity_at']);

        $onlineThreshold = now()->subMinutes(3);

        $result = $users->map(function ($user) use ($onlineThreshold) {
            return [
                'id' => $user->id,
                'is_online' => $user->last_activity_at && $user->last_activity_at->gte($onlineThreshold),
                'last_activity_at' => $user->last_activity_at,
            ];
        });

        return response()->json(['users' => $result]);
    }
}
