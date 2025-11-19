<?php

namespace App\Services\Chat\Repositories;

interface ChatPresenceRepositoryInterface
{
    public function touch(int $roomId, string $clientId): void;

    public function countActive(int $roomId, int $seconds = 30): int;
}