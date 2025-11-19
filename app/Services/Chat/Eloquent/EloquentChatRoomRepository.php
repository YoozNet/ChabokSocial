<?php

namespace App\Services\Chat\Eloquent;

use App\Services\Chat\Repositories\ChatRoomRepositoryInterface;
use Illuminate\Support\Collection;
use App\Models\ChatRoom;

class EloquentChatRoomRepository implements ChatRoomRepositoryInterface
{
    public function create(array $data): ChatRoom
    {
        return ChatRoom::create($data);
    }

    public function findBySlug(string $slug): ?ChatRoom
    {
        return ChatRoom::where('slug', $slug)
            ->first();
    }

    public function existsBySlug(string $slug): bool
    {
        return ChatRoom::where('slug', $slug)
            ->exists();
    }

    public function reservedSlugs(): Collection
    {
        return collect([
            'admin',
            'login',
            'api',
            'dashboard',
            'logout',
            '404'
        ]);
    }

    public function deactivate(ChatRoom $room): void
    {
        $room->update(['status' => 'inactive']);
    }
}