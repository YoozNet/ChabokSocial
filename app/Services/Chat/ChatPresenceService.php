<?php

namespace App\Services\Chat;

use App\Services\Chat\Repositories\ChatPresenceRepositoryInterface;

class ChatPresenceService
{
    public function __construct(
        private ChatPresenceRepositoryInterface $presence,
    ) {}

    public function ping(int $roomId, string $clientId): void
    {
        $this->presence->touch($roomId, $clientId);
    }

    public function countActive(int $roomId): int
    {
        return $this->presence->countActive($roomId, 30);
    }
}