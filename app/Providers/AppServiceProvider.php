<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

use App\Services\Chat\Repositories\ChatPresenceRepositoryInterface;
use App\Services\Chat\Repositories\ChatMessageRepositoryInterface;
use App\Services\Chat\Repositories\ChatRoomRepositoryInterface;

use App\Services\Chat\Eloquent\EloquentChatPresenceRepository;
use App\Services\Chat\Eloquent\EloquentChatMessageRepository;
use App\Services\Chat\Eloquent\EloquentChatRoomRepository;

use App\Services\Installer\Repositories\AdminRepositoryInterface;
use App\Services\Installer\Eloquent\EloquentAdminRepository;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->bind(ChatRoomRepositoryInterface::class, EloquentChatRoomRepository::class);
        $this->app->bind(ChatMessageRepositoryInterface::class, EloquentChatMessageRepository::class);
        $this->app->bind(ChatPresenceRepositoryInterface::class, EloquentChatPresenceRepository::class);
        $this->app->bind(AdminRepositoryInterface::class, EloquentAdminRepository::class);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        if (!defined('CURL_SSLVERSION_TLSv1_2')) {
            define('CURL_SSLVERSION_TLSv1_2', 6);
        }
    }
}
