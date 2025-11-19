<?php
use App\Http\Controllers\Admin\Auth\AdminLoginController;
use App\Http\Controllers\Admin\AdminChatRoomController;
use App\Http\Controllers\ChatMessageController;
use App\Http\Controllers\ChatRoomController;
use Illuminate\Support\Facades\Route;
/*
    |--------------------------------------------------------------------------
    | Chat routes
    |--------------------------------------------------------------------------
*/

Route::name('chat.')->group(function () {

    // Public / initial actions
    Route::controller(ChatRoomController::class)->group(function () {
        Route::get('/', 'createForm')->name('create-form');
        Route::post('/chat-rooms', 'store')->name('store');
        Route::post('/join', 'joinRoom')->name('join');
    });

    // Routes scoped by room
    Route::prefix('r/{slug}')->group(function () {
        // Chat room view & forms
        Route::controller(ChatRoomController::class)->group(function () {
            Route::get('/', 'show')->name('show');

            Route::get('/password', 'passwordForm')->name('password.form');
            Route::post('/password', 'checkPassword')->name('password.check');

            Route::get('/nickname', 'nicknameForm')->name('nickname.form');
            Route::post('/nickname', 'saveNickname')->name('nickname.save');
        });

        // Messages & presence
        Route::controller(ChatMessageController::class)->group(function () {

            // Messages + Ajax
            Route::prefix('messages')->name('messages.')->group(function () {
                Route::get('/', 'index')->name('index');
                Route::post('/', 'store')->name('store');
            });

            // Presence
            Route::name('presence.')->group(function () {
                Route::post('ping', 'ping')->name('ping');
                Route::get('active-count', 'activeCount')->name('count');
            });
        });
    });
});

Route::middleware('signed')->group(function () {
    Route::get('files/{path}', [App\Http\Controllers\StorageController::class, 'local'])->where('path', '.*')->name('file.local');
});

/*
    |--------------------------------------------------------------------------
    | Admin routes
    |--------------------------------------------------------------------------
*/

Route::prefix('admin')->name('admin.')->group(function () {

    // Auth
    Route::middleware('guest:admin')->group(function () {
        Route::get('/login', [AdminLoginController::class, 'showLoginForm'])->name('login');
        Route::post('/login', [AdminLoginController::class, 'login'])->name('login.attempt');
    });

    Route::post('logout', [AdminLoginController::class, 'logout'])
        ->middleware('auth:admin')
        ->name('logout');

    // Protected admin area
    Route::middleware('auth:admin')->group(function () {
        Route::get('chat-rooms', [AdminChatRoomController::class, 'index'])->name('chat-rooms.index');
        Route::get('chat-rooms/{room}', [AdminChatRoomController::class, 'show'])->name('chat-rooms.show');

        // لیست پیام‌های اتاق برای ادمین
        Route::get('chat-rooms/{room}/messages', [AdminChatRoomController::class, 'messages'])
            ->name('chat-rooms.messages');

        // اکشن‌های مدیریت اتاق
        Route::post('chat-rooms/{room}/deactivate', [AdminChatRoomController::class, 'deactivate'])
            ->name('chat-rooms.deactivate');
        Route::post('chat-rooms/{room}/activate', [AdminChatRoomController::class, 'activate'])
            ->name('chat-rooms.activate');
        Route::post('chat-rooms/{room}/extend-expire', [AdminChatRoomController::class, 'extendExpire'])
            ->name('chat-rooms.extend-expire');
        Route::delete('chat-rooms/{room}', [AdminChatRoomController::class, 'destroy'])
            ->name('chat-rooms.destroy');
    });
});