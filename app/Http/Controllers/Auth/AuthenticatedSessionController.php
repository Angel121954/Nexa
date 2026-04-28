<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class AuthenticatedSessionController extends Controller
{
    /**
     * Display the login view.
     */
    public function create(): View
    {
        return view('auth.login');
    }

    /**
     * Handle an incoming authentication request.
     */
    public function store(LoginRequest $request)
    {
        $request->authenticate();

        $request->session()->regenerate();

        $user = Auth::user();

        // Si el perfil ya está completo → feed principal
        if ($user->profile?->profile_completed) {
            return redirect()->route('explore.index');
        }

        // Si está a mitad del onboarding, reanudar donde quedó
        $step = $user->profile?->onboarding_step ?? 0;

        return match(true) {
            $step >= 3 => redirect()->route('onboarding.preferences'),
            $step >= 2 => redirect()->route('onboarding.photos'),
            default    => redirect()->route('onboarding.basic'),
        };
    }

    /**
     * Destroy an authenticated session.
     */
    public function destroy(Request $request): RedirectResponse
    {
        Auth::guard('web')->logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        return redirect('/');
    }
}
