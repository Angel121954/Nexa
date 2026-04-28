<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Illuminate\View\View;
use Laravel\Socialite\Facades\Socialite;

class RegisteredUserController extends Controller
{
    // ─────────────────────────────────────────
    // Registro tradicional
    // ─────────────────────────────────────────

    public function create(): View
    {
        return view('auth.register');
    }

    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'name'     => ['required', 'string', 'max:255'],
            'email'    => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
        ]);

        $user = User::create([
            'name'             => $request->name,
            'email'            => $request->email,
            'password'         => Hash::make($request->password),
            'onboarding_step'  => 1,
        ]);

        event(new Registered($user));
        Auth::login($user);

        return redirect()->route('onboarding.basic');
    }

    // ─────────────────────────────────────────
    // Google OAuth
    // ─────────────────────────────────────────

    public function redirectToGoogle(): RedirectResponse
    {
        return Socialite::driver('google')->redirect();
    }

    public function handleGoogleCallback()
    {
        try {
            $googleUser = Socialite::driver('google')->stateless()->user();
        } catch (\Exception $e) {
            return redirect()->route('login')
                ->withErrors(['email' => 'No se pudo autenticar con Google. Intenta de nuevo.']);
        }

        // Busca por google_id primero, luego por email
        $user = User::where('google_id', $googleUser->getId())->first()
            ?? User::where('email', $googleUser->getEmail())->first();

        if ($user) {
            // Vincula google_id si no lo tenía (registro previo con email)
            if (!$user->google_id) {
                $user->update(['google_id' => $googleUser->getId()]);
            }
        } else {
            // Usuario nuevo
            $user = User::create([
                'name'      => $googleUser->getName(),
                'email'     => $googleUser->getEmail(),
                'google_id' => $googleUser->getId(),
                'avatar'    => $googleUser->getAvatar(),
                'password'  => null,
            ]);

            event(new Registered($user));
        }

        Auth::login($user, remember: true);

        return $user->profile_completed
            ? redirect()->route('explore.index')
            : redirect()->route('onboarding.basic');
    }

    // ─────────────────────────────────────────
    // Facebook OAuth
    // ─────────────────────────────────────────

    public function redirectToFacebook(): RedirectResponse
    {
        return Socialite::driver('facebook')->redirect();
    }

    public function handleFacebookCallback()
    {
        try {
            $fbUser = Socialite::driver('facebook')->stateless()->user();
        } catch (\Exception $e) {
            return redirect()->route('login')
                ->withErrors(['email' => 'No se pudo autenticar con Facebook. Intenta de nuevo.']);
        }

        $user = User::where('facebook_id', $fbUser->getId())->first()
            ?? User::where('email', $fbUser->getEmail())->first();

        if ($user) {
            if (!$user->facebook_id) {
                $user->update(['facebook_id' => $fbUser->getId()]);
            }
        } else {
            $user = User::create([
                'name'        => $fbUser->getName(),
                'email'       => $fbUser->getEmail(),
                'facebook_id' => $fbUser->getId(),
                'avatar'      => $fbUser->getAvatar(),
                'password'    => null,
            ]);

            event(new Registered($user));
        }

        Auth::login($user, remember: true);

        return View('onboarding.algo');
    }
}
