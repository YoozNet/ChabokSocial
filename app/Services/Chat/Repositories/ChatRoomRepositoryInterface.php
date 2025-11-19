<?php

namespace App\Services\Chat\Repositories;

use App\Models\ChatRoom;
use Illuminate\Support\Collection;

interface ChatRoomRepositoryInterface
{
    public function create(array $data): ChatRoom;

    public function findBySlug(string $slug): ?ChatRoom;

    public function existsBySlug(string $slug): bool;

    public function reservedSlugs(): Collection;

    public function deactivate(ChatRoom $room): void;
}