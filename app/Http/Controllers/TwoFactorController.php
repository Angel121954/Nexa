<?php

namespace App\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Laravel\Fortify\Actions\ConfirmTwoFactorAuthentication;
use Laravel\Fortify\Actions\DisableTwoFactorAuthentication;
use Laravel\Fortify\Actions\EnableTwoFactorAuthentication;
use Laravel\Fortify\Actions\GenerateNewRecoveryCodes;
use Laravel\Fortify\Contracts\TwoFactorAuthenticationProvider;
use Laravel\Fortify\Fortify;

class TwoFactorController extends Controller
{
    public function show()
    {
        $user = auth()->user();

        $hasSecret = !is_null($user->two_factor_secret);
        $confirmed = !is_null($user->two_factor_confirmed_at);

        if ($hasSecret) {
            return response()->json([
                'enabled' => $confirmed,
                'confirmed' => $confirmed,
                'qr_code' => $user->twoFactorQrCodeSvg(),
                'secret' => Fortify::currentEncrypter()->decrypt($user->two_factor_secret),
                'recovery_codes' => $user->recoveryCodes(),
            ]);
        }

        return response()->json([
            'enabled' => false,
            'confirmed' => false,
        ]);
    }

    public function setup(Request $request)
    {
        $user = auth()->user();

        if ($user->hasEnabledTwoFactorAuthentication()) {
            return response()->json(['message' => '2FA ya está activado.'], 422);
        }

        app(EnableTwoFactorAuthentication::class)($user);

        $user->refresh();

        return response()->json([
            'qr_code' => $user->twoFactorQrCodeSvg(),
            'secret' => Fortify::currentEncrypter()->decrypt($user->two_factor_secret),
            'recovery_codes' => $user->recoveryCodes(),
        ]);
    }

    public function confirm(Request $request)
    {
        $request->validate(['code' => 'required|string']);

        $user = auth()->user();

        app(ConfirmTwoFactorAuthentication::class)($user, $request->input('code'));

        if (session('errors') && session('errors')->has('code')) {
            return back()->withErrors(['code' => 'El código ingresado no es válido.']);
        }

        return back()->with('status', 'two-factor-confirmed');
    }

    public function confirmJson(Request $request)
    {
        $request->validate(['code' => 'required|string']);

        $user = auth()->user();

        $provider = app(TwoFactorAuthenticationProvider::class);
        $secret = Fortify::currentEncrypter()->decrypt($user->two_factor_secret);

        if (!$provider->verify($secret, $request->input('code'))) {
            return response()->json(['message' => 'Código inválido.'], 422);
        }

        $user->forceFill(['two_factor_confirmed_at' => now()])->save();

        return response()->json(['message' => '2FA confirmado correctamente.']);
    }

    public function disable(Request $request)
    {
        app(DisableTwoFactorAuthentication::class)(auth()->user());

        return back()->with('status', 'two-factor-disabled');
    }

    public function disableJson(Request $request)
    {
        app(DisableTwoFactorAuthentication::class)(auth()->user());

        return response()->json(['message' => '2FA desactivado.']);
    }

    public function recoveryCodes()
    {
        $user = auth()->user();

        if (!$user->hasEnabledTwoFactorAuthentication()) {
            return response()->json(['message' => '2FA no está activado.'], 422);
        }

        app(GenerateNewRecoveryCodes::class)($user);

        $user->refresh();

        return response()->json([
            'recovery_codes' => $user->recoveryCodes(),
        ]);
    }
}
