<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\UserController;
use App\Http\Controllers\Api\FriendshipController;
use App\Http\Controllers\Api\MessageController;

// Publikus útvonalak
Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);

// Email verifikáció
Route::get('/email/verify/{id}/{hash}', [AuthController::class, 'verifyEmail'])
    ->name('verification.verify');


// Autentikált útvonalak
Route::middleware(['auth:sanctum'])->group(function () {
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/me', [AuthController::class, 'me']);
    
    // Csak verifikált felhasználóknak
    Route::middleware('verified')->group(function () {
        Route::get('/users', [UserController::class, 'index']);
        Route::post('/friends/add', [FriendshipController::class, 'addFriend']);
        Route::get('/friends', [FriendshipController::class, 'friends']);
        Route::post('/messages/send', [MessageController::class, 'send']);
        Route::get('/messages/conversation/{userId}', [MessageController::class, 'conversation']);
    });
});