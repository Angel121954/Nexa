<?php

use App\Http\Controllers\Api\MessageController;
use Illuminate\Support\Facades\Route;

Route::middleware('auth')->group(function () {
    Route::get('/unread-messages-count', [MessageController::class, 'unreadCount']);

    Route::get('/matches/{matchId}/messages', [MessageController::class, 'index']);
    Route::post('/matches/{matchId}/messages', [MessageController::class, 'store']);
    Route::post('/matches/{matchId}/messages/mark-read', [MessageController::class, 'markAsRead']);
});
