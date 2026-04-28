<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Services\CloudinaryService;
use Laravel\Socialite\Facades\Socialite;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;

class SocialAuthController extends Controller
{
    protected array $allowedProviders = ['google', 'facebook'];

    public function redirect(string $provider)
    {
        if (!in_array($provider, $this->allowedProviders)) {
            abort(404);
        }

        return Socialite::driver($provider)->redirect();
    }

    protected function resolveAvatar(string $provider, $socialUser): string
    {
        $avatar = $socialUser->getAvatar();

        return match ($provider) {
            'facebook' => preg_replace('/type=\w+/', 'type=large', $avatar),
            'google'   => preg_replace('/=s\d+-c/', '=s400-c', $avatar),
            default    => $avatar,
        };
    }

    public function callback(string $provider, CloudinaryService $cloudinary)
    {
        if (!in_array($provider, $this->allowedProviders)) {
            abort(404);
        }

        try {
            $socialUser = Socialite::driver($provider)->user();

            if (!$socialUser->getEmail()) {
                return redirect()
                    ->route('login')
                    ->with('error', 'Tu cuenta no tiene email disponible.');
            }

            $avatarOriginal = $this->resolveAvatar($provider, $socialUser);

            $user = User::where('email', $socialUser->getEmail())->first();

            if (!$user) {
                $baseUsername = Str::slug($socialUser->getName() ?? $socialUser->getNickname() ?? 'user');
                $baseUsername = $baseUsername ?: 'user';
                $username = $baseUsername;
                $count = 1;

                while (User::where('username', $username)->exists()) {
                    $username = $baseUsername . $count++;
                }

                $user = User::create([
                    'name'              => $socialUser->getName() ?? $socialUser->getNickname(),
                    'username'          => $username,
                    'email'             => $socialUser->getEmail(),
                    'email_verified_at' => now(),
                    'password'          => bcrypt(Str::random(16)),
                    'onboarding_step'   => 1,
                ]);
            }

            // Descargar y subir avatar a Cloudinary: home/nexa/avatares/user_{id}
            $avatarUrl = $cloudinary->uploadAvatarFromUrl($avatarOriginal, $user->id);

            $user->update([
                'avatar' => $avatarUrl,
            ]);

            Auth::login($user, true);
            request()->session()->regenerate();

            return redirect()->route('onboarding.basic');
        } catch (\Exception $e) {
            report($e);

            return redirect()
                ->route('login')
                ->with('error', 'Error al autenticar con ' . ucfirst($provider));
        }
    }
}