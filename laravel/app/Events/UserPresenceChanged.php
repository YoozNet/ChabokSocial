<?php

namespace App\Events;

use App\Models\User;
use Illuminate\Broadcasting\Channel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Queue\SerializesModels;
use Illuminate\Broadcasting\InteractsWithSockets;

class UserPresenceChanged implements ShouldBroadcastNow
{
    use InteractsWithSockets, SerializesModels;

    public User $user;

    public function __construct(User $user)
    {
        $this->user = $user;
    }

    public function broadcastOn(): Channel
    {
        return new Channel('user.presence');
    }

    public function broadcastWith(): array
    {
        return [
            'user_id'   => $this->user->id,
            'username'  => $this->user->username,
            'is_online' => $this->user->is_online,
            'last_seen' => $this->user->last_seen?->toISOString()
        ];
    }

    public function broadcastAs(): string
    {
        return 'presence.changed';
    }
}
