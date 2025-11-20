<?php
use App\Http\Controllers\Admin\Auth\AdminLoginController;
use App\Http\Controllers\Admin\AdminChatRoomController;
use App\Http\Controllers\Installer\InstallController;
use Illuminate\Support\Facades\{Auth, Http, Route};
use App\Http\Controllers\ChatMessageController;
use App\Http\Controllers\ChatRoomController;
use App\Http\Controllers\StorageController;

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
    Route::get('files/{path}', [StorageController::class, 'local'])->where('path', '.*')->name('file.local');
});

/*
    |--------------------------------------------------------------------------
    | Admin routes
    |--------------------------------------------------------------------------
*/

Route::prefix('admin')->name('admin.')->group(function () {

    // Auth
    Route::controller(AdminLoginController::class)->group(function () {
        Route::prefix('login')->middleware('guest:admin')->group(function () {
            Route::get('/', 'showLoginForm')->name('login');
            Route::post('/', 'login')->name('login.attempt');
        });
        
        Route::post('logout', 'logout')->middleware('auth:admin')->name('logout');
    });


    Route::middleware('auth:admin')->prefix('chat-rooms')->name('chat-rooms.')->controller(AdminChatRoomController::class)->group(function () {
        Route::get('/', 'index')->name('index');
        Route::prefix('{room}')->group(function () {
            Route::get('/', 'show')->name('show');
            Route::get('/messages', 'messages')->name('messages');

            // Actions
            Route::post('/deactivate', 'deactivate')->name('deactivate');
            Route::post('/activate', 'activate')->name('activate');
            Route::post('/extend-expire', 'extendExpire')->name('extend-expire');
            Route::delete('/', 'destroy')->name('destroy');
        });
    });
});

/*
    |--------------------------------------------------------------------------
    | Install routes
    |--------------------------------------------------------------------------
*/

Route::prefix('install')->name('install.')->group(function () {
    Route::get('/', [InstallController::class, 'intro'])->name('intro');

    Route::get('/preflight', [InstallController::class, 'showPreflight'])->name('preflight');
    Route::post('/preflight', [InstallController::class, 'runPreflight'])->name('preft');

    Route::get('/env', [InstallController::class, 'showEnv'])->name('env');
    Route::post('/env', [InstallController::class, 'applyEnv']);

    Route::get('/database', [InstallController::class, 'showDatabase'])->name('db');
    Route::post('/database', [InstallController::class, 'applyDatabase'])->name('database');

    Route::get('/admin', [InstallController::class, 'showAdmin'])->name('admin');
    Route::post('/admin', [InstallController::class, 'createAdmin']);

    Route::get('/done', [InstallController::class, 'done'])->name('done');
});