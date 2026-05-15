<?php

namespace App\Http\Controllers;

use App\Events\MatchCreated;
use App\Events\NotificationCreated;
use App\Models\Interest;
use App\Models\Like;
use App\Models\Notification;
use App\Models\User;
use App\Models\UserMatch;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class ExploreController extends Controller
{
    // ── Feed principal ─────────────────────────
    public function index(Request $request): View|JsonResponse
    {
        $me = auth()->user();

        $query = User::with(['profile', 'interests', 'photos'])
            ->whereHas('profile', fn($q) => $q->where('profile_completed', true))
            ->where('id', '!=', $me->id)
            ->whereDoesntHave('blocksReceived', fn($q) => $q->where('blocker_id', $me->id))
            ->whereDoesntHave('blocksSent', fn($q) => $q->where('blocked_id', $me->id));

        // Filtro: búsqueda por nombre, bio o ciudad
        if ($search = $request->get('q')) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhereHas(
                        'profile',
                        fn($p) => $p
                            ->where('bio', 'like', "%{$search}%")
                            ->orWhere('city', 'like', "%{$search}%")
                    );
            });
        }

        // Filtro: ciudad
        if ($city = $request->get('city')) {
            $query->whereHas('profile', fn($q) => $q->where('city', 'like', "%{$city}%"));
        }

        // Filtro: género
        if ($gender = $request->get('gender')) {
            $query->whereHas('profile', fn($q) => $q->where('gender', $gender));
        }

        // Filtro: edad (min-max)
        if ($ageMin = $request->get('age_min')) {
            $maxDate = now()->subYears((int) $ageMin)->format('Y-m-d');
            $query->whereHas('profile', fn($q) => $q->where('birth_date', '<=', $maxDate));
        }
        if ($ageMax = $request->get('age_max')) {
            $minDate = now()->subYears((int) $ageMax + 1)->addDay()->format('Y-m-d');
            $query->whereHas('profile', fn($q) => $q->where('birth_date', '>=', $minDate));
        }

        // Filtro: intereses
        if ($interestIds = $request->get('interests')) {
            $ids = is_array($interestIds) ? $interestIds : explode(',', $interestIds);
            $ids = array_filter(array_map('intval', $ids));
            if ($ids) {
                $query->whereHas('interests', fn($q) => $q->whereIn('interests.id', $ids));
            }
        }
        //  FILTRO: personas cercanas
        if ($request->get('nearby')) {

            if ($me->latitude && $me->longitude) {

                $lat = $me->latitude;
                $lng = $me->longitude;
                $radius = (int) $request->get('distance', 10);

                $query->selectRaw("
            users.*,
            (6371 * acos(
                cos(radians(?))
                * cos(radians(latitude))
                * cos(radians(longitude) - radians(?))
                + sin(radians(?))
                * sin(radians(latitude))
            )) AS distance
        ", [$lat, $lng, $lat])
                    ->whereNotNull('latitude')
                    ->whereNotNull('longitude')
                    ->having('distance', '<=', $radius)
                    ->orderBy('distance');
            }
        }

        // Filtro rápido: "tab"
        $tab = $request->get('tab', 'all');
        match ($tab) {
            // Usuarios que me dieron like (aparecen primero)
            'liked_me' => $query->whereHas('likesSent', fn($q) => $q->where('receiver_id', $me->id))
                ->orderByDesc('id'),
            // Ordenar por fecha de creación descendente
            'new'      => $query->orderByDesc('created_at'),
            // Mismos intereses → priorizar
            'interests' => $query->withCount([
                'interests as shared_interests' => fn($q) =>
                $q->whereIn('interests.id', $me->interests->pluck('id')->toArray())
            ])
                ->orderByDesc('shared_interests'),
            default => $request->get('nearby') ? $query : $query->inRandomOrder(),
        };

        $users = $query->paginate(12)->withQueryString();

        // IDs de usuarios a los que ya di like
        $likedIds = $me->likesSent()->pluck('receiver_id')->toArray();

        // IDs de matches
        $matchIds = UserMatch::where('user1_id', $me->id)
            ->orWhere('user2_id', $me->id)
            ->get()
            ->map(fn($m) => $m->user1_id === $me->id ? $m->user2_id : $m->user1_id)
            ->toArray();

        $interests = Interest::orderBy('name')->get();

        if ($request->wantsJson()) {
            $html = view('explore._cards', compact('users', 'likedIds', 'matchIds'))->render();

            return response()->json([
                'html'  => $html,
                'count' => $users->total(),
            ]);
        }

        return view('explore.index', compact('users', 'likedIds', 'matchIds', 'interests', 'tab'));
    }

    // ── Toggle Like (AJAX) ──────────────────────
    public function like(Request $request, int $userId): JsonResponse
    {
        $me = auth()->user();

        if ($me->id === $userId) {
            return response()->json(['error' => 'No puedes darte like a ti mismo'], 422);
        }

        if ($me->hasBlocked($userId) || $me->isBlockedBy($userId)) {
            return response()->json(['error' => 'No puedes interactuar con este usuario'], 422);
        }

        $target = User::findOrFail($userId);

        $existing = Like::where('sender_id', $me->id)->where('receiver_id', $userId)->first();

        if ($existing) {
            $existing->delete();
            return response()->json(['liked' => false, 'match' => false]);
        }

        Like::create(['sender_id' => $me->id, 'receiver_id' => $userId]);

        $isMatch = Like::where('sender_id', $userId)->where('receiver_id', $me->id)->exists();

        if ($isMatch) {
            $match = UserMatch::where(function ($q) use ($me, $userId) {
                $q->where('user1_id', $me->id)->where('user2_id', $userId);
            })->orWhere(function ($q) use ($me, $userId) {
                $q->where('user1_id', $userId)->where('user2_id', $me->id);
            })->first();

            if (!$match) {
                $match = UserMatch::create([
                    'user1_id' => min($me->id, $userId),
                    'user2_id' => max($me->id, $userId),
                ]);
                event(new MatchCreated($match));
            }

            foreach ([$me->id, $userId] as $uid) {
                $notif = Notification::create([
                    'user_id' => $uid,
                    'type'    => 'match',
                    'data'    => [
                        'actor_name'   => $uid === $me->id ? $target->name : $me->name,
                        'actor_avatar' => $uid === $me->id ? $target->avatar : $me->avatar,
                        'message'      => '¡Tienes un nuevo match!',
                        'action_url'   => route('messages.index'),
                    ],
                ]);
                $unread = Notification::where('user_id', $uid)->whereNull('read_at')->count();
                $total = Notification::where('user_id', $uid)->count();
                broadcast(new NotificationCreated($notif, $unread, $total))->toOthers();
            }
        } else {
            $notif = Notification::create([
                'user_id' => $userId,
                'type'    => 'like',
                'data'    => [
                    'actor_name'   => $me->name,
                    'actor_avatar' => $me->avatar,
                    'message'      => 'te ha dado like.',
                    'action_url'   => route('profile.show', $me->id),
                ],
            ]);
            $unread = Notification::where('user_id', $userId)->whereNull('read_at')->count();
            $total = Notification::where('user_id', $userId)->count();
            broadcast(new NotificationCreated($notif, $unread, $total))->toOthers();
        }

        return response()->json([
            'liked'     => true,
            'match'     => $isMatch,
            'matchName' => $isMatch ? $target->name : null,
        ]);
    }
}
