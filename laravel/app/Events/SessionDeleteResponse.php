<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Queue\SerializesModels;

class SessionDeleteResponse implements ShouldBroadcastNow
{
    use InteractsWithSockets, SerializesModels;

    public int $userId;
    public bool $success;
    public string $message;

    public function __construct(int $userId, bool $success, string $message)
    {
        $this->userId  = $userId;
        $this->success = $success;
        $this->message = $message;
    }

    public function broadcastOn()
    {
        return new Channel("user.{$this->userId}");
    }

    public function broadcastAs()
    {
        return "session.delete.response";
    }

    public function broadcastWith()
    {
        return [
            'user_id' => $this->userId,
            'success' => $this->success,
            'message' => $this->message
        ];
    }
}
