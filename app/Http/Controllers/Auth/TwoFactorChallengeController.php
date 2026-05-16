<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;
use Laravel\Fortify\Contracts\TwoFactorAuthenticationProvider;
use Laravel\Fortify\Fortify;

class TwoFactorChallengeController extends Controller
{
    public function create(Request $request)
    {
        if (!$request->session()->has('login.id')) {
            return redirect()->route('login');
        }

        return view('auth.two-factor-challenge');
    }

    public function store(Request $request)
    {
        $request->validate([
            'code' => 'nullable|string',
            'recovery_code' => 'nullable|string',
        ]);

        $userId = $request->session()->get('login.id');
        $user = User::find($userId);

        if (!$user) {
            return redirect()->route('login');
        }

        $valid = false;

        if ($code = $request->input('code')) {
            $provider = app(TwoFactorAuthenticationProvider::class);
            $secret = Fortify::currentEncrypter()->decrypt($user->two_factor_secret);
            $valid = $provider->verify($secret, $code);
        } elseif ($recoveryCode = $request->input('recovery_code')) {
            $codes = $user->recoveryCodes();
            $valid = collect($codes)->first(fn($c) => hash_equals($c, $recoveryCode)) !== null;
            if ($valid) {
                $user->replaceRecoveryCode($recoveryCode);
            }
        }

        if (!$valid) {
            $field = $request->input('code') ? 'code' : 'recovery_code';
            $msg = $field === 'code'
                ? 'El código proporcionado no es válido.'
                : 'El código de recuperación no es válido.';
            throw ValidationException::withMessages([$field => $msg]);
        }

        Auth::guard('web')->loginUsingId($userId);
        $request->session()->forget('login.id');

        if ($user->profile?->profile_completed) {
            return redirect()->route('explore.index');
        }

        $step = $user->profile?->onboarding_step ?? 0;
        return match (true) {
            $step >= 3 => redirect()->route('onboarding.preferences'),
            $step >= 2 => redirect()->route('onboarding.photos'),
            default => redirect()->route('onboarding.basic'),
        };
    }
}
