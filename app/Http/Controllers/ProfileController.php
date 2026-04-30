<?php

namespace App\Http\Controllers;

use App\Models\UserPhoto;
use App\Models\Like;
use App\Models\User;
use App\Http\Requests\ProfileUpdateRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Illuminate\View\View;
use Illuminate\Support\Facades\Storage;


class ProfileController extends Controller
{
    public function index(): View
    {
        $user = auth()->user()->load(['profile', 'interests', 'photos']);

        if (!$user->profile) {
            $user->profile()->create([]);
        }

        // Personas a quienes yo di like
        $likedUsers = User::whereIn('id', $user->likesSent()->pluck('receiver_id'))
            ->with('profile')
            ->get();

        // Personas que me dieron like a mí
        $admirers = User::whereHas('likesSent', fn($q) => $q->where('receiver_id', $user->id))
            ->with('profile')
            ->get();

        return view('profile.index', [
            'user'       => $user,
            'profile'    => $user->profile,
            'likedUsers' => $likedUsers,
            'admirers'   => $admirers,
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

        // Update user fields (only name and email belong to users table)
        $user->fill($request->only([
            'name',
            'email',
        ]));

        if ($user->isDirty('email')) {
            $user->email_verified_at = null;
        }

        $user->save();

        // Update or create profile fields
        $profile = $user->profile ?? $user->profile()->create([]);

        $profile->fill($request->only([
            'bio',
            'city',
            'birth_date',
            'gender',
            'pronouns',
            'looking_for',
        ]));

        $profile->save();

        return back()->with('status', 'profile-updated');
    }

    public function destroy(Request $request): RedirectResponse
    {
        $request->validateWithBag('userDeletion', [
            'password' => ['required', 'current_password'],
        ]);

        $user = $request->user();

        Auth::logout();
        $user->delete();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return Redirect::to('/');
    }

    //  SUBIR FOTO 
    public function uploadPhoto(Request $request)
    {
        if (auth()->user()->photos()->count() >= 6) {
            return back()->with('error', 'Máximo 6 fotos');
        }
        $request->validate([
            'photo' => 'required|image|max:2048'
        ]);

        $path = $request->file('photo')->store('photos', 'public');

        UserPhoto::create([
            'user_id' => auth()->id(),
            'path' => $path
        ]);

        return back();
    }
    public function deletePhoto($id)
    {
        $photo = UserPhoto::where('id', $id)
            ->where('user_id', auth()->id())
            ->firstOrFail();

        if (Storage::disk('public')->exists($photo->path)) {
            Storage::disk('public')->delete($photo->path);
        }

        $photo->delete();
        return back()->with('success', 'Foto eliminada correctamente');
    }

    // ── Actualizar avatar ──────────────────────
    public function updateAvatar(Request $request)
    {
        $request->validate(['avatar' => 'required|image|max:4096']);
        $user = auth()->user();

        $path = $request->file('avatar')->store('avatars', 'public');
        $user->update(['avatar' => Storage::url($path)]);

        return back()->with('status', 'avatar-updated');
    }

    // ── Actualizar banner ──────────────────────
    public function updateBanner(Request $request)
    {
        $request->validate(['banner' => 'required|image|max:6144']);
        $user  = auth()->user();
        $profile = $user->profile ?? $user->profile()->create([]);

        $path = $request->file('banner')->store('banners', 'public');
        $profile->update(['banner' => Storage::url($path)]);

        return back()->with('status', 'banner-updated');
    }

    // ── Actualizar intereses ───────────────────
    public function updateInterests(Request $request)
    {
        $request->validate(['interests' => 'nullable|array', 'interests.*' => 'integer|exists:interests,id']);
        auth()->user()->interests()->sync($request->input('interests', []));
        return back()->with('status', 'interests-updated');
    }
}
