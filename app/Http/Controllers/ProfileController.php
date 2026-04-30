<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\UserPhoto;
use App\Services\CloudinaryService;
use App\Http\Requests\ProfileUpdateRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Log;
use Illuminate\View\View;
use Illuminate\Support\Facades\Storage;


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

    public function show(User $user): View
    {
        if ($user->id === auth()->id()) {
            return redirect()->route('profile.index');
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

    public function uploadAvatar(Request $request, CloudinaryService $cloudinary)
    {
        $request->validate([
            'avatar' => 'required|image|max:5120',
        ]);

        $user = auth()->user();

        if ($user->avatar_public_id) {
            try {
                $cloudinary->delete($user->avatar_public_id);
            } catch (\Exception $e) {
                Log::warning('Failed to delete old avatar: ' . $e->getMessage());
            }
        }

        $avatar = $cloudinary->uploadAvatar($request->file('avatar'), $user->id);

        $user->update([
            'avatar'           => $avatar['url'],
            'avatar_public_id' => $avatar['public_id'],
        ]);

        if ($request->expectsJson()) {
            return response()->json(['message' => 'avatar-updated', 'avatar' => $avatar['url']]);
        }

        return back()->with('status', 'avatar-updated');
    }

    public function deletePhoto($id)
    {
        $photo = UserPhoto::where('id', $id)
            ->where('user_id', auth()->id())
            ->firstOrFail();

        // borrar archivo físico
        if (Storage::disk('public')->exists($photo->path)) {
            Storage::disk('public')->delete($photo->path);
        }

        // borrar registro BD
        $photo->delete();

        return back()->with('success', 'Foto eliminada correctamente');
    }
}
