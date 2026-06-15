<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\UserPhoto;
use App\Services\CloudinaryService;
use App\Http\Requests\ProfileUpdateRequest;
use App\Events\UserBlocked;
use App\Models\Block;
use App\Models\Report;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Log;
use Illuminate\View\View;

class ProfileController extends Controller
{
    public function index(): View
    {
        $user = auth()->user();

        if (!$user->profile) {
            $user->profile()->create([]);
        }

        return view('profile.index', [
            'user' => $user,
            'profile' => $user->profile
        ]);
    }

    public function show(User $user): View|RedirectResponse
    {
        if ($user->id === auth()->id()) {
            return redirect()->route('profile.index');
        }

        $me = auth()->user();
        if ($me->hasBlocked($user->id) || $me->isBlockedBy($user->id)) {
            return redirect()->route('explore.index')->with('error', 'Este usuario no está disponible.');
        }

        return view('profile.show', [
            'user' => $user,
            'profile' => $user->profile,
        ]);
    }

    public function edit(Request $request): View
    {
        $user = $request->user();

        if (!$user->profile) {
            $user->profile()->create([]);
        }

        return view('profile.edit', [
            'user' => $user,
            'profile' => $user->profile,
        ]);
    }

    public function update(ProfileUpdateRequest $request): RedirectResponse
    {
        $user = $request->user();

        // Datos básicos
        $user->fill($request->only(['name', 'email']));

        if ($user->isDirty('email')) {
            $user->email_verified_at = null;
        }

        $user->save();

        // Perfil
        $profile = $user->profile ?? $user->profile()->create([]);

        $profile->fill($request->only([
            'bio',
            'city',
            'birth_date',
            'gender',
            'pronouns',
            'gender_preference',
        ]));

        $profile->save();

        return back()->with('status', 'profile-updated');
    }

    public function destroy(Request $request, CloudinaryService $cloudinary): RedirectResponse
    {
        $request->validateWithBag('userDeletion', [
            'password' => ['required', 'current_password'],
        ]);

        $user = $request->user();

        // Eliminar avatar de Cloudinary
        if ($user->avatar_public_id) {
            try {
                $cloudinary->delete($user->avatar_public_id);
            } catch (\Exception $e) {
                Log::warning('Error eliminando avatar de Cloudinary: ' . $e->getMessage());
            }
        }

        // Eliminar fotos de galería de Cloudinary
        foreach ($user->photos as $photo) {
            if ($photo->public_id) {
                try {
                    $cloudinary->delete($photo->public_id);
                } catch (\Exception $e) {
                    Log::warning("Error eliminando foto {$photo->id} de Cloudinary: " . $e->getMessage());
                }
            }
        }

        Auth::logout();
        $user->delete();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return Redirect::to('/');
    }

    public function uploadPhoto(Request $request, CloudinaryService $cloudinary)
    {
        if (auth()->user()->photos()->count() >= 6) {
            if ($request->expectsJson()) {
                return response()->json(['message' => 'Máximo 6 fotos'], 422);
            }
            return back()->with('error', 'Máximo 6 fotos');
        }

        $request->validate([
            'photo'      => 'nullable|image|max:5120',
            'photo_url'  => 'nullable|url',
        ]);

        try {
            $userId = auth()->id();

            if ($request->filled('photo_url')) {
                $upload = $cloudinary->uploadGalleryFromUrl(
                    $request->input('photo_url'),
                    $userId,
                    $userId . '_' . time()
                );
            } else {
                $request->validate([
                    'photo' => 'required|image|max:5120'
                ]);

                $upload = $cloudinary->uploadGallery(
                    $request->file('photo'),
                    $userId,
                    $userId . '_' . time()
                );
            }

            $photo = UserPhoto::create([
                'user_id'   => $userId,
                'path'      => $upload['url'],
                'public_id' => $upload['public_id']
            ]);

            if ($request->expectsJson()) {
                return response()->json(['message' => 'Foto subida', 'url' => $upload['url'], 'id' => $photo->id]);
            }

            return back()->with('success', 'Foto subida');
        } catch (\Exception $e) {
            Log::error('Error subiendo foto: ' . $e->getMessage());

            if ($request->expectsJson()) {
                return response()->json(['message' => 'Error subiendo foto'], 422);
            }

            return back()->with('error', 'Error subiendo foto');
        }
    }

    public function updateAvatar(Request $request, CloudinaryService $cloudinary)
    {
        $request->validate([
            'avatar'     => 'nullable|image|max:5120',
            'avatar_url' => 'nullable|url',
        ]);

        $user = auth()->user();

        try {
            if ($user->avatar_public_id) {
                $cloudinary->delete($user->avatar_public_id);
            }

            if ($request->filled('avatar_url')) {
                $avatar = $cloudinary->uploadAvatarFromUrl(
                    $request->input('avatar_url'),
                    $user->id
                );
            } else {
                $request->validate([
                    'avatar' => 'required|image|max:5120',
                ]);

                $avatar = $cloudinary->uploadAvatar(
                    $request->file('avatar'),
                    $user->id
                );
            }

            $user->update([
                'avatar'           => $avatar['url'],
                'avatar_public_id' => $avatar['public_id'],
            ]);

            if ($request->expectsJson()) {
                return response()->json([
                    'message' => 'avatar-updated',
                    'avatar'  => $avatar['url']
                ]);
            }

            return back()->with('status', 'avatar-updated');
        } catch (\Exception $e) {
            Log::error('Error avatar: ' . $e->getMessage());
            return back()->with('error', 'Error actualizando avatar');
        }
    }
    public function deletePhoto($id, CloudinaryService $cloudinary)
    {
        $photo = UserPhoto::where('id', $id)
            ->where('user_id', auth()->id())
            ->firstOrFail();

        try {
            if ($photo->public_id) {
                $cloudinary->delete($photo->public_id);
            }

            $photo->delete();

            return back()->with('success', 'Foto eliminada');
        } catch (\Exception $e) {
            Log::error('Error eliminando foto: ' . $e->getMessage());
            return back()->with('error', 'Error eliminando foto');
        }
    }

    public function block(Request $request, User $user): RedirectResponse|\Illuminate\Http\JsonResponse
    {
        if ($user->id === auth()->id()) {
            return $request->expectsJson()
                ? response()->json(['error' => 'No puedes bloquearte a ti mismo.'], 422)
                : back()->with('error', 'No puedes bloquearte a ti mismo.');
        }

        $existing = Block::where('blocker_id', auth()->id())
            ->where('blocked_id', $user->id)
            ->first();

        if ($existing) {
            $existing->delete();
            broadcast(new UserBlocked($user->id, false, auth()->id()));
            return $request->expectsJson()
                ? response()->json(['message' => 'Usuario desbloqueado.', 'blocked' => false])
                : back()->with('success', 'Usuario desbloqueado.');
        }

        Block::create([
            'blocker_id' => auth()->id(),
            'blocked_id' => $user->id,
        ]);

        broadcast(new UserBlocked($user->id, true, auth()->id()));

        return $request->expectsJson()
            ? response()->json(['message' => 'Usuario bloqueado.', 'blocked' => true])
            : back()->with('success', 'Usuario bloqueado.');
    }

    public function report(Request $request, User $user): RedirectResponse|\Illuminate\Http\JsonResponse
    {
        if ($user->id === auth()->id()) {
            return $request->expectsJson()
                ? response()->json(['error' => 'No puedes reportarte a ti mismo.'], 422)
                : back()->with('error', 'No puedes reportarte a ti mismo.');
        }

        $validated = $request->validate([
            'reason'      => 'required|string|max:255',
            'description' => 'nullable|string|max:1000',
        ]);

        Report::create([
            'reporter_id' => auth()->id(),
            'reported_id' => $user->id,
            'reason'      => $validated['reason'],
            'description' => $validated['description'] ?? null,
        ]);

        return $request->expectsJson()
            ? response()->json(['message' => 'Reporte enviado.'])
            : back()->with('success', 'Reporte enviado.');
    }
}
