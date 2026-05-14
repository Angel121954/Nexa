<?php

use App\Http\Controllers\Auth\RegisteredUserController;
use App\Http\Controllers\ExploreController;
use App\Http\Controllers\MessagePageController;
use App\Http\Controllers\OnboardingController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\Api\LikeController;
use App\Http\Controllers\Api\MatchController;
use App\Http\Controllers\Api\MessageController;
use App\Http\Controllers\Api\UserController;
use Illuminate\Support\Facades\Broadcast;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\LocationController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\StoryController;

// Rutas de broadcasting (para autenticación de canales privados)
Broadcast::routes(['middleware' => ['web']]);

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

    Route::get('/messages', [MessagePageController::class, 'index'])->name('messages.index');
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
    Route::post('/profile/avatar', [ProfileController::class, 'updateAvatar'])->name('profile.avatar');

    // Bloquear / desbloquear usuario
    Route::post('/profile/{user}/block', [ProfileController::class, 'block'])->name('profile.block');
    Route::post('/profile/{user}/report', [ProfileController::class, 'report'])->name('profile.report');

    // Notifications
    Route::get('/notifications', [NotificationController::class, 'index'])->name('notifications.index');
    Route::get('/notifications/unread-count', [NotificationController::class, 'unreadCount'])->name('notifications.unread');
    Route::patch('/notifications/{notification}/read', [NotificationController::class, 'read'])->name('notifications.read');
    Route::patch('/notifications/read-all', [NotificationController::class, 'readAll'])->name('notifications.read-all');
    Route::patch('/notifications/preferences', [NotificationController::class, 'preferences'])->name('notifications.preferences');
    Route::delete('/notifications/{notification}', [NotificationController::class, 'destroy'])->name('notifications.destroy');
});

Route::middleware('auth')->prefix('api')->group(function () {  // ← con prefix
    // Likes
    Route::post('/likes', [LikeController::class, 'store']);
    Route::delete('/likes/{receiverId}', [LikeController::class, 'destroy']);

    // Matches
    Route::get('/matches', [MatchController::class, 'index']);
    Route::get('/matches/{id}', [MatchController::class, 'show']);
    Route::delete('/matches/{id}', [MatchController::class, 'destroy']);

    // Messages
    Route::get('/unread-messages-count', [MessageController::class, 'unreadCount']);
    Route::get('/matches/{matchId}/messages', [MessageController::class, 'index']);
    Route::post('/matches/{matchId}/messages', [MessageController::class, 'store']);
    Route::post('/matches/{matchId}/messages/read', [MessageController::class, 'markAsRead']);
    Route::post('/matches/{matchId}/messages/mark-read', [MessageController::class, 'markAsRead']);

    // Estado de usuarios online (fallback con last_activity_at)
    Route::get('/users/online-status', [\App\Http\Controllers\Api\UserController::class, 'onlineStatus']);

    // Ubicación del usuario
    Route::post('/update-location', [LocationController::class, 'update']);

    // Stories
    Route::get('/stories', [StoryController::class, 'index']);
    Route::get('/stories/user/{userId}', [StoryController::class, 'userStories']);
    Route::post('/stories', [StoryController::class, 'store']);
    Route::delete('/stories/{story}', [StoryController::class, 'destroy']);
    Route::post('/stories/{story}/seen', [StoryController::class, 'markSeen']);
});

// Página legal (términos y privacidad)
Route::view('/legal', 'legal.legal')->name('legal');

require __DIR__ . '/auth.php';
