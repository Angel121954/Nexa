<?php

namespace App\Http\Controllers;

use App\Models\Interest;
use App\Models\UserPhoto;
use App\Services\CloudinaryService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class OnboardingController extends Controller
{
    // ── Paso 2: Perfil básico ──────────────────
    public function basic(): View
    {
        return view('onboarding.basic', [
            'user' => auth()->user(),
        ]);
    }

    public function storeBasic(Request $request): RedirectResponse
    {
        $request->validate([
            'bio'        => ['nullable', 'string', 'max:160'],
            'city'       => ['required', 'string', 'max:100'],
            'birth_date' => ['required', 'date', 'before:-18 years'],
            'gender'     => ['required', 'in:male,female,non_binary,other'],
            'pronouns'   => ['nullable', 'string', 'max:50'],
        ], [
            'birth_date.before' => 'Debes tener al menos 18 años.',
        ]);

        auth()->user()->update([
            'bio'             => $request->bio,
            'city'            => $request->city,
            'birth_date'      => $request->birth_date,
            'gender'          => $request->gender,
            'pronouns'        => $request->pronouns,
            'onboarding_step' => 2,
        ]);

        return redirect()->route('onboarding.photos');
    }

    // ── Paso 3: Foto y galería ─────────────────
    public function photos(): View
    {
        return view('onboarding.photos', [
            'user'   => auth()->user(),
            'photos' => auth()->user()->photos,
        ]);
    }

    public function storePhotos(Request $request, CloudinaryService $cloudinary): RedirectResponse
    {
        $request->validate([
            'avatar'    => ['required', 'image', 'max:5120', 'mimes:jpg,jpeg,png'],
            'gallery.*' => ['nullable', 'image', 'max:5120', 'mimes:jpg,jpeg,png'],
        ]);

        $user = auth()->user();

        // 🔥 ELIMINAR AVATAR ANTERIOR (CORRECTO)
        if ($user->avatar_public_id) {
            try {
                $cloudinary->delete($user->avatar_public_id);
            } catch (\Exception $e) {
                Log::warning('Failed to delete old avatar: ' . $e->getMessage());
            }
        }

        // 🔥 SUBIR NUEVO AVATAR
        $avatar = $cloudinary->uploadAvatar($request->file('avatar'), $user->id);

        $user->update([
            'avatar'             => $avatar['url'],
            'avatar_public_id'   => $avatar['public_id'],
            'onboarding_step'    => 3,
        ]);

        // 🔥 GALERÍA
        if ($request->hasFile('gallery')) {
            $currentCount = $user->photos()->count();

            foreach ($request->file('gallery') as $index => $photo) {
                if ($currentCount + $index >= 6) break;

                $uploaded = $cloudinary->uploadGallery($photo, $user->id, $currentCount + $index);

                $user->photos()->create([
                    'path'       => $uploaded['url'],
                    'public_id'  => $uploaded['public_id'],
                    'sort_order' => $currentCount + $index,
                ]);
            }
        }

        return redirect()->route('onboarding.preferences');
    }

    public function deletePhoto(UserPhoto $photo, CloudinaryService $cloudinary): RedirectResponse
    {
        abort_if($photo->user_id !== auth()->id(), 403);
        
        // Eliminar de Cloudinary si es URL de Cloudinary
        if ($photo->path && str_contains($photo->path, 'cloudinary')) {
            try {
                $publicId = $this->extractPublicId($photo->path);
                $cloudinary->delete($publicId);
            } catch (\Exception $e) {
                Log::warning('Failed to delete photo from Cloudinary: ' . $e->getMessage());
            }
        }
        
        $photo->delete();
        return back();
    }

    private function extractPublicId(string $url): string
    {
        // Cloudinary URL format: https://res.cloudinary.com/cloud_name/image/upload/.../public_id.jpg
        // We need to extract just the public_id part
        if (preg_match('/\/([^/]+)\.[a-z]+$/', $url, $matches)) {
            $publicId = $matches[1];
            // Remove transformation prefix if present (e.g., c_fill,w_300...)
            if (str_contains($publicId, '/')) {
                $parts = explode('/', $publicId);
                $publicId = end($parts);
            }
            return $publicId;
        }
        return '';
    }

    // ── Paso 4: Preferencias ───────────────────
    public function preferences(): View
    {
        return view('onboarding.preferences', [
            'interests'         => Interest::all(),
            'selectedInterests' => auth()->user()->interests->pluck('id')->toArray(),
        ]);
    }

    public function storePreferences(Request $request): RedirectResponse
    {
        $request->validate([
            'interests'    => ['nullable', 'array', 'max:10'],
            'interests.*'  => ['exists:interests,id'],
            'looking_for' => ['nullable', 'array'],
            'looking_for.*' => ['in:friends,dating,networking,activities'],
        ]);

        $user = auth()->user();
        $user->interests()->sync($request->interests ?? []);
        $user->update([
            'looking_for'      => $request->looking_for ?? [],
            'profile_completed' => true,
            'onboarding_step'  => 4,
        ]);

        return redirect()->route('onboarding.welcome');
    }

    // ── Bienvenida ─────────────────────────────
    public function welcome(): View
    {
        return view('onboarding.welcome');
    }
}
