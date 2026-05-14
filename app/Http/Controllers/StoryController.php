<?php

namespace App\Http\Controllers;

use App\Models\Story;
use App\Models\StoryView;
use App\Services\CloudinaryService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class StoryController extends Controller
{
    public function __construct(
        private CloudinaryService $cloudinary
    ) {}

    public function index(): JsonResponse
    {
        $me = auth()->user();

        $matchUserIds = $me->matchedUsers()->pluck('users.id');

        $visibleIds = collect([$me->id])
            ->merge($matchUserIds)
            ->unique();

        $stories = Story::with(['user:id,name,avatar'])
            ->active()
            ->whereIn('user_id', $visibleIds)
            ->orderBy('created_at', 'desc')
            ->get()
            ->groupBy('user_id')
            ->map(function ($userStories, $userId) use ($me) {
                $first = $userStories->first();
                $allViewed = $userStories->every(fn($s) => $s->isViewedBy($me->id));

                return [
                    'user' => [
                        'id'     => $first->user->id,
                        'name'   => $first->user->name,
                        'avatar' => $first->user->avatar_url,
                    ],
                    'stories'    => $userStories->map(fn($s) => [
                        'id'         => $s->id,
                        'media_url'  => $s->media_path,
                        'created_at' => $s->created_at->diffForHumans(),
                        'viewed'     => $s->isViewedBy($me->id),
                    ]),
                    'all_viewed' => $allViewed,
                ];
            })->values();

        return response()->json($stories);
    }

    public function userStories(int $userId): JsonResponse
    {
        $me = auth()->user();

        if ($userId !== $me->id && !$me->isMatchedWith($userId)) {
            return response()->json(['error' => 'No autorizado.'], 403);
        }

        $stories = Story::with('user:id,name,avatar')
            ->active()
            ->where('user_id', $userId)
            ->orderBy('created_at', 'asc')
            ->get();

        if ($stories->isEmpty()) {
            return response()->json(['error' => 'No hay stories activas.'], 404);
        }

        $user = $stories->first()->user;

        return response()->json([
            'user' => [
                'id'     => $user->id,
                'name'   => $user->name,
                'avatar' => $user->avatar_url,
            ],
            'stories' => $stories->map(fn($s) => [
                'id'         => $s->id,
                'media_url'  => $s->media_path,
                'created_at' => $s->created_at->diffForHumans(),
                'viewed'     => $s->isViewedBy($me->id),
            ]),
        ]);
    }

    public function store(Request $request): JsonResponse
    {
        $request->validate([
            'media'     => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:10240',
            'media_url' => 'nullable|url|max:2048',
        ]);

        if (!$request->hasFile('media') && !$request->filled('media_url')) {
            return response()->json(['error' => 'Debes subir una imagen o proporcionar una URL.'], 422);
        }

        $me = auth()->user();

        try {
            if ($request->hasFile('media')) {
                $result = $this->cloudinary->uploadStory($request->file('media'), $me->id);
            } else {
                $result = $this->cloudinary->uploadStoryFromUrl($request->input('media_url'), $me->id);
            }

            $story = Story::create([
                'user_id'    => $me->id,
                'media_path' => $result['url'],
                'expires_at' => now()->addHours(24),
            ]);

            broadcast(new \App\Events\StoryCreated($story))->toOthers();

            return response()->json([
                'message' => 'Story publicada.',
                'story'   => [
                    'id'         => $story->id,
                    'media_url'  => $story->media_path,
                    'expires_at' => $story->expires_at,
                ],
            ], 201);
        } catch (\Exception $e) {
            Log::error('Error al subir story:', ['error' => $e->getMessage()]);
            return response()->json(['error' => 'Error al subir la story: ' . $e->getMessage()], 500);
        }
    }

    public function destroy(Story $story): JsonResponse
    {
        if ($story->user_id !== auth()->id()) {
            return response()->json(['error' => 'No autorizado.'], 403);
        }

        $story->views()->delete();
        $story->delete();

        return response()->json(['message' => 'Story eliminada.']);
    }

    public function markSeen(Request $request, Story $story): JsonResponse
    {
        $me = auth()->user();

        if ($story->user_id === $me->id) {
            return response()->json(['error' => 'No puedes ver tu propia story.'], 422);
        }

        if (!$me->isMatchedWith($story->user_id)) {
            return response()->json(['error' => 'No autorizado.'], 403);
        }

        StoryView::firstOrCreate([
            'story_id' => $story->id,
            'user_id'  => $me->id,
        ], [
            'viewed_at' => now(),
        ]);

        return response()->json(['message' => 'Vista registrada.']);
    }
}
