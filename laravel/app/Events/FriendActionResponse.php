<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Queue\SerializesModels;
use App\Models\User;

class FriendActionResponse implements ShouldBroadcastNow
{
    use InteractsWithSockets, SerializesModels;

    public User $user;
    public string $broadcastEvent;
    public array $payload;

    public function __construct(User $user, string $broadcastEvent, array $payload = [])
    {
        $this->user = $user;
        $this->broadcastEvent = $broadcastEvent;
        $this->payload = $payload;
    }

    public function broadcastOn()
    {
        return new Channel("user.{$this->user->id}");
    }

    public function broadcastAs()
    {
        return $this->broadcastEvent;
    }

    public function broadcastWith()
    {
        return array_merge([
            'user_id' => $this->user->id,
        ], $this->payload);
    }
}
