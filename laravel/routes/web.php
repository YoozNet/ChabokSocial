<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\ChatController;
use App\Http\Controllers\FriendController;
use App\Http\Controllers\StatusController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\BackupCodeController;

/*
|--------------------------------------------------------------------------
| Public Routes
|--------------------------------------------------------------------------
*/

Route::get('/', fn() => view('welcome'))->name('welcome');

Route::prefix('register')->group(function () {
    Route::get('/', [AuthController::class, 'showRegisterForm'])->name('register.form');
    Route::post('/', [AuthController::class, 'register'])->name('register.submit');
    Route::post('/verify', [AuthController::class, 'verify'])->name('verify.submit');
});

Route::prefix('login')->group(function () {
    Route::get('/', [AuthController::class, 'showLoginForm'])->name('login');
    Route::post('/', [AuthController::class, 'login'])->name('login.submit');
    Route::post('/verify', [AuthController::class, 'verifyLogin'])->name('login.verify');
});

Route::post('/logout', function() {
    Auth::logout();
    request()->session()->invalidate();
    request()->session()->regenerateToken();
    return redirect('/');
})->name('logout');

/*
|--------------------------------------------------------------------------
| Authenticated Routes
|--------------------------------------------------------------------------
*/

Route::middleware('auth')->group(function() {

    // Dashboard
    Route::get('/dashboard', fn() => view('dashboard'))->name('dashboard');

    // Profile
    Route::prefix('profile')->group(function () {
        Route::post('/update', [ProfileController::class, 'update']);
        Route::get('/avatar', [ProfileController::class, 'downloadAvatar']);

    });
    
    Route::get('/avatar/{userId}', [\App\Http\Controllers\AvatarController::class, 'show'])->whereNumber('userId');

    // Status
    Route::post('/status', [StatusController::class, 'update']);

    // Friends
    Route::prefix('friends')->group(function () {
        Route::get('/', [FriendController::class, 'index']);
        Route::get('/status/{id}', [FriendController::class, 'status']);
        Route::post('/delete', [FriendController::class, 'removeFriend']);
        Route::post('/request', [FriendController::class, 'sendRequest']);
        Route::post('/accept', [FriendController::class, 'acceptRequest']);
        Route::post('/decline', [FriendController::class, 'declineRequest']);
        Route::post('/favorite', [FriendController::class, 'toggleFavorite']);
        Route::get('/user-by-username/{username}', function($username) {
            $user = \App\Models\User::select('id','name','username','avatar','is_online','last_seen')
                ->where('username',$username)
                ->first();

            if (!$user) {
                return response()->json(['error' => 'کاربر پیدا نشد'], 404);
            }

            return response()->json($user);
        });
    });

    // Chat
    Route::prefix('chat')->group(function () {
        Route::post('/send', [ChatController::class, 'sendMessage']);
        Route::post('/clear-conversation', [ChatController::class, 'clearConversation']);
        Route::post('/{id}', [ChatController::class, 'deleteMessage']);
        Route::patch('/{id}', [ChatController::class, 'updateMessage']);
    });

    // Attachments
    Route::get('/attachment/{message}', [ChatController::class, 'downloadAttachment']);

    // BackUp
    Route::prefix('backup-codes')->group(function () {
        Route::get('/', [BackupCodeController::class, 'index']);          // نمایش لیست (index)
        Route::post('/generate', [BackupCodeController::class, 'generate']); // تولید 10 تایی اولیه
        Route::post('/regenerate', [BackupCodeController::class, 'regenerate']); // ریجنریت
    });
});
