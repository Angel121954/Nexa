<?php

use App\Http\Controllers\Api\MessageController;
use Illuminate\Support\Facades\Route;

Route::get('/unread-messages-count', [MessageController::class, 'unreadCount']);

