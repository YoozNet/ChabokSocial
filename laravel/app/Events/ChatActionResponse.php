<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Queue\SerializesModels;

class ChatActionResponse implements ShouldBroadcastNow
{
    use InteractsWithSockets, SerializesModels;

    public $user;
    public string $broadcastEvent;
    public array $payload;

    public function __construct($userID, string $broadcastEvent, array $payload = [])
    {
        $this->user = $userID;
        $this->broadcastEvent = $broadcastEvent;
        $this->payload = $payload;
    }

    public function broadcastOn()
    {
        return new Channel("user.{$this->user}");
    }

    public function broadcastAs()
    {
        return $this->broadcastEvent;
    }

    public function broadcastWith()
    {
        return array_merge([
            'user_id' => $this->user,
        ], $this->payload);
    }
}
