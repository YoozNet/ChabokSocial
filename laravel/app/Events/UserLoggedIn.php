<?php

namespace App\Events;

use App\Models\User;
use Illuminate\Broadcasting\Channel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Queue\SerializesModels;
use Illuminate\Broadcasting\InteractsWithSockets;

class UserLoggedIn implements ShouldBroadcastNow
{
    use InteractsWithSockets, SerializesModels;

    public User $user;

    /**
     * Create a new event instance.
     */
    public function __construct(User $user)
    {
        $this->user = $user;
    }

    /**
     * The name of the channel.
     */
    public function broadcastOn(): Channel
    {
        return new Channel('user.login');
    }

    /**
     * The data to broadcast.
     */
    public function broadcastWith(): array
    {
        return [
            'user_id'  => $this->user->id,
            'username' => $this->user->username,
            'message'  => 'شما وارد حساب خود شدید 🎉'
        ];
    }

    /**
     * Optional: event name
     */
    public function broadcastAs(): string
    {
        return 'user.loggedin';
    }
}
