<?php

use App\Http\Controllers\Auth\RegisteredUserController;
use App\Http\Controllers\ExploreController;
use App\Http\Controllers\OnboardingController;
use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;

Route::get('/', fn() => view('auth.login'));

// Google OAuth
Route::get('auth/google',          [RegisteredUserController::class, 'redirectToGoogle'])->name('google.redirect');
Route::get('auth/google/callback', [RegisteredUserController::class, 'handleGoogleCallback'])->name('google.callback');

// Facebook OAuth
Route::get('auth/facebook',          [RegisteredUserController::class, 'redirectToFacebook'])->name('facebook.redirect');
Route::get('auth/facebook/callback', [RegisteredUserController::class, 'handleFacebookCallback'])->name('facebook.callback');

// Onboarding (requiere auth, sin profile_completed obligatorio)
Route::middleware('auth')->prefix('onboarding')->name('onboarding.')->group(function () {
    Route::get('basic',                [OnboardingController::class, 'basic'])->name('basic');
    Route::post('basic',               [OnboardingController::class, 'storeBasic'])->name('basic.store');

    Route::get('photos',               [OnboardingController::class, 'photos'])->name('photos');
    Route::post('photos',              [OnboardingController::class, 'storePhotos'])->name('photos.store');
    Route::delete('photos/{photo}',    [OnboardingController::class, 'deletePhoto'])->name('photos.delete');

    Route::get('preferences',          [OnboardingController::class, 'preferences'])->name('preferences');
    Route::post('preferences',         [OnboardingController::class, 'storePreferences'])->name('preferences.store');

    Route::get('welcome',              [OnboardingController::class, 'welcome'])->name('welcome');
});

// App principal
Route::middleware(['auth'])->group(function () {
    Route::get('/explore',              [ExploreController::class, 'index'])->name('explore.index');
    Route::post('/explore/like/{user}', [ExploreController::class, 'like'])->name('explore.like');

    Route::get('/messages', fn() => view('messages.index'))->name('messages.index');
    // VER PERFIL
    Route::get('/profile', [ProfileController::class, 'index'])->name('profile.index');
    Route::get('/profile/{user}', [ProfileController::class, 'show'])->name('profile.show');

    // EDITAR PERFIL
    Route::get('/profile/edit', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
    Route::post('/profile/photo', [ProfileController::class, 'uploadPhoto'])->name('profile.photo');
    Route::delete('/profile/photo/{id}', [ProfileController::class, 'deletePhoto'])
        ->name('profile.photo.delete');
});

require __DIR__ . '/auth.php';
