<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Queue\SerializesModels;

class SessionListUpdated implements ShouldBroadcastNow
{
    use InteractsWithSockets, SerializesModels;

    public int $userId;
    public $sessions;

    public function __construct(int $userId, $sessions)
    {
        $this->userId = $userId;
        $this->sessions = $sessions;
    }

    public function broadcastOn()
    {
        return new Channel("user.{$this->userId}");
    }

    public function broadcastAs()
    {
        return "session.list";
    }

    public function broadcastWith()
    {
        return [
            'user_id' => $this->userId,
            'sessions' => $this->sessions
        ];
    }
}
