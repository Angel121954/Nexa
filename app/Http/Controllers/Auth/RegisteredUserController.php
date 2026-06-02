<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Mail\VerificationCodeMail;
use App\Models\EmailVerificationCode;
use App\Models\Profile;
use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
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
        $pendingGoogle = session('pending_google_user');

        return view('auth.register', [
            'pendingGoogle' => $pendingGoogle,
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $pendingGoogle = session('pending_google_user');

        $rules = [
            'name'              => ['required', 'string', 'max:255'],
            'email'             => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'verification_code' => ['required', 'string', 'size:6'],
        ];

        if (!$pendingGoogle) {
            $rules['password'] = ['required', 'confirmed', Rules\Password::defaults()];
        }

        $request->validate($rules);

        $code = EmailVerificationCode::where('email', $request->email)
            ->where('code', $request->verification_code)
            ->where('used', false)
            ->where('expires_at', '>', now())
            ->first();

        if (!$code) {
            return back()->withErrors([
                'verification_code' => 'El código de verificación no es válido o ha expirado.',
            ])->onlyInput('email', 'name');
        }

        $code->update(['used' => true]);

        $user = User::create([
            'name'      => $request->name,
            'email'     => $request->email,
            'password'  => $pendingGoogle ? null : Hash::make($request->password),
            'google_id' => $pendingGoogle['google_id'] ?? null,
            'avatar'    => $pendingGoogle['avatar'] ?? null,
        ]);

        $user->profile()->create([
            'onboarding_step' => 1,
        ]);

        session()->forget('pending_google_user');

        event(new Registered($user));
        Auth::login($user);

        return redirect()->route('onboarding.basic');
    }

    // ─────────────────────────────────────────
    // Enviar código de verificación al correo
    // ─────────────────────────────────────────

    public function sendVerificationCode(Request $request): JsonResponse
    {
        $validator = validator($request->only('email'), [
            'email' => ['required', 'string', 'email', 'max:255'],
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => $validator->errors()->first('email'),
            ]);
        }

        $existingUser = User::where('email', $request->email)->first();
        if ($existingUser) {
            return response()->json([
                'success' => false,
                'message' => 'Este correo ya está registrado.',
            ]);
        }

        // Invalidar códigos anteriores no usados
        EmailVerificationCode::where('email', $request->email)
            ->where('used', false)
            ->where('expires_at', '>', now())
            ->update(['used' => true]);

        $code = str_pad((string) random_int(0, 999999), 6, '0', STR_PAD_LEFT);

        EmailVerificationCode::create([
            'email'      => $request->email,
            'code'       => $code,
            'expires_at' => now()->addMinutes(10),
        ]);

        Mail::to($request->email)->send(new VerificationCodeMail($code));

        return response()->json([
            'success' => true,
            'message' => 'Código enviado a tu correo.',
        ]);
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

        $user = User::where('google_id', $googleUser->getId())->first()
            ?? User::where('email', $googleUser->getEmail())->first();

        if ($user) {
            if (!$user->google_id) {
                $user->update(['google_id' => $googleUser->getId()]);
            }

            Auth::login($user, remember: true);

            if ($user->hasEnabledTwoFactorAuthentication()) {
                request()->session()->put('login.id', $user->id);
                Auth::logout();
                return redirect()->route('two-factor.challenge');
            }

            return ($user->profile?->profile_completed ?? false)
                ? redirect()->route('explore.index')
                : redirect()->route('onboarding.basic');
        }

        session()->put('pending_google_user', [
            'name'      => $googleUser->getName(),
            'email'     => $googleUser->getEmail(),
            'google_id' => $googleUser->getId(),
            'avatar'    => $googleUser->getAvatar(),
        ]);

        return redirect()->route('register', ['google' => 1]);
    }

    // ─────────────────────────────────────────
    // Facebook OAuth
    // ─────────────────────────────────────────

    public function redirectToFacebook(): RedirectResponse
    {
        return Socialite::driver('facebook')
            ->scopes(['public_profile'])
            ->redirect();
    }

    public function handleFacebookCallback()
    {
        try {
            $fbUser = Socialite::driver('facebook')->stateless()->user();
        } catch (\Exception $e) {
            return redirect()->route('login')
                ->withErrors(['email' => 'No se pudo autenticar con Facebook. Intenta de nuevo.']);
        }

        $fbEmail = $fbUser->getEmail();
        if (!$fbEmail) {
            $fbEmail = 'fb_' . $fbUser->getId() . '@facebook.nexa';
        }

        $user = User::where('facebook_id', $fbUser->getId())->first()
            ?? User::where('email', $fbEmail)->first();

        if ($user) {
            if (!$user->facebook_id) {
                $user->update(['facebook_id' => $fbUser->getId()]);
            }

            Auth::login($user, remember: true);

            if ($user->hasEnabledTwoFactorAuthentication()) {
                request()->session()->put('login.id', $user->id);
                Auth::logout();
                return redirect()->route('two-factor.challenge');
            }

            return ($user->profile?->profile_completed ?? false)
                ? redirect()->route('explore.index')
                : redirect()->route('onboarding.basic');
        }

        session()->put('pending_facebook_user', [
            'name'        => $fbUser->getName(),
            'email'       => $fbEmail,
            'facebook_id' => $fbUser->getId(),
            'avatar'      => $fbUser->getAvatar(),
        ]);

        return redirect()->route('register', ['facebook' => 1]);
    }
}
