<?php

namespace App\Services\Chat\Eloquent;

use App\Services\Chat\Repositories\ChatPresenceRepositoryInterface;
use App\Models\ChatRoomParticipant;
use Illuminate\Support\Carbon;

class EloquentChatPresenceRepository implements ChatPresenceRepositoryInterface
{
    public function touch(int $roomId, string $clientId): void
    {
        ChatRoomParticipant::updateOrCreate(
                [
                    'chat_room_id' => $roomId,
                    'client_id' => $clientId,
                ],
                [
                    'last_seen_at' => Carbon::now(),
                ]
            );
    }

    public function countActive(int $roomId, int $seconds = 30): int
    {
        $threshold = Carbon::now()->subSeconds($seconds);

        return ChatRoomParticipant::where('chat_room_id', $roomId)
            ->where('last_seen_at', '>=', $threshold)
            ->count();
    }
}