<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

use App\Services\Chat\Repositories\ChatPresenceRepositoryInterface;
use App\Services\Chat\Repositories\ChatMessageRepositoryInterface;
use App\Services\Chat\Repositories\ChatRoomRepositoryInterface;
use App\Services\Chat\Eloquent\EloquentChatPresenceRepository;
use App\Services\Chat\Eloquent\EloquentChatMessageRepository;
use App\Services\Chat\Eloquent\EloquentChatRoomRepository;

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
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }
}
