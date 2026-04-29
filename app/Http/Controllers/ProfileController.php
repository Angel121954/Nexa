<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProfileUpdateRequest;
use App\Models\Profile;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Illuminate\View\View;

class ProfileController extends Controller
{
    /**
     * Mostrar perfil
     */
    public function index(): View
    {
        $user = auth()->user();

        //  asegurar que tenga profile
        if (!$user->profile) {
            $user->profile()->create([]);
        }

        return view('profile.index', [
            'user' => $user,
            'profile' => $user->profile
        ]);
    }

    /**
     * Editar perfil
     */
    public function edit(Request $request): View
    {
        $user = $request->user();

        //  asegurar profile también aquí
        if (!$user->profile) {
            $user->profile()->create([]);
        }

        return view('profile.edit', [
            'user' => $user,
            'profile' => $user->profile,
        ]);
    }

    /**
     * Actualizar perfil
     */
    public function update(ProfileUpdateRequest $request): RedirectResponse
    {
        $user = $request->user();
        //  actualizar usuario
        $user->fill($request->validated());

        if ($user->isDirty('email')) {
            $user->email_verified_at = null;
        }

        $user->save();

        //  crear profile si no existe
        $profile = $user->profile ?? $user->profile()->create([]);

        //  actualizar profile
        $profile->fill($request->only([
            'bio',
            'city',
            'birth_date',
            'gender',
            'pronouns'
        ]));

        $profile->save();

        return Redirect::route('profile.edit')
            ->with('status', 'profile-updated');
    }

    /**
     * Eliminar cuenta
     */
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
}
