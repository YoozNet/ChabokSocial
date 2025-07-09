<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Redis;
use App\Models\User;
use App\Models\Friend;
use App\Models\Message;

class FriendListener extends Command
{
    protected $signature = 'friend:listen';
    protected $description = 'Listen for friend events via Redis';

    public function handle()
    {
        $this->info("Friend listener started...");

        try {
            $redis = new \Redis();
            $connected = $redis->connect(env('REDIS_HOST'), env('REDIS_PORT'), 5); // timeout 5s

            if (!$connected) {
                $this->error("Could not connect to Redis at " . env('REDIS_HOST') . ":" . env('REDIS_PORT'));
                return;
            }

            if (!$redis->auth(env('REDIS_PASSWORD'))) {
                $this->error("Redis AUTH failed.");
                return;
            }

            $this->info("Connected to Redis, listening for events...");

            $redis->psubscribe([
                "friend:list",
                "friend:request",
                "friend:accept",
                "friend:decline",
                "friend:remove",
                "friend:favorite"
            ], function ($redis, $pattern, $channel, $message) {
                try {
                    $data = json_decode($message, true);
                    $this->processMessage($channel, $data);
                } catch (\Throwable $e) {
                    $this->error("Error processing friend message: " . $e->getMessage());
                }
            });

        } catch (\RedisException $e) {
            $this->error("Redis Exception: " . $e->getMessage());
            return;
        } catch (\Throwable $e) {
            $this->error("General Exception: " . $e->getMessage());
            return;
        }
    }

    protected function processMessage($channel, $data)
    {
        try {
            switch ($channel) {
                case "friend:list":
                    $this->handleFriendList($data['user_id']);
                    break;
                case "friend:request":
                    $this->handleFriendRequest($data);
                    break;
                case "friend:accept":
                    $this->handleFriendAccept($data);
                    break;
                case "friend:decline":
                    $this->handleFriendDecline($data);
                    break;
                case "friend:remove":
                    $this->handleFriendRemove($data);
                    break;
                case "friend:favorite":
                    $this->handleFriendFavorite($data);
                    break;
            }
        } catch (\Throwable $e) {
            $this->info("FriendListener error: " . $e->getMessage());
        }
    }

    protected function handleFriendList($userId)
    {
        try {
            $user = User::find($userId);
            if (!$user) return;

            $cacheKey = "user_friends_{$user->id}";
            $friendsData = Cache::remember($cacheKey, now()->addSeconds(30), function() use ($user) {
                $pendingRequests = Friend::with('user')
                    ->where('friend_id', $user->id)
                    ->where('status', 'pending')
                    ->orderBy('created_at', 'desc')
                    ->take(5)
                    ->get();

                $acceptedFriends = Friend::with(['user', 'friend'])
                    ->where('status', 'accepted')
                    ->where(function($q) use ($user) {
                        $q->where('user_id', $user->id)
                        ->orWhere('friend_id', $user->id);
                    })
                    ->get()
                    ->map(function($row) use ($user) {
                        $friend = $row->user_id == $user->id ? $row->friend : $row->user;

                        $friend->unread_count = Message::where('from_user_id', $friend->id)
                            ->where('to_user_id', $user->id)
                            ->where('is_read', 0)
                            ->count();

                        $friend->is_favorite = $row->is_favorite ?? false;

                        return $friend;
                    })
                    ->sortByDesc(fn($f) => $f->unread_count)
                    ->sortByDesc(fn($f) => $f->is_online)
                    ->sortByDesc(fn($f) => $f->is_favorite)
                    ->values();

                $sentRequests = Friend::with('friend')
                    ->where('user_id', $user->id)
                    ->where('status', '!=', 'accepted')
                    ->orderBy('created_at', 'desc')
                    ->take(3)
                    ->get()
                    ->map(function ($item) {
                        if ($item->relationLoaded('friend') && $item->friend) {
                            $item->friend->makeHidden('password');
                        }
                        return $item;
                    });

                return [
                    'user_id' => $user->id,
                    'pending' => $pendingRequests,
                    'friends' => $acceptedFriends,
                    'sent'    => $sentRequests,
                ];
            });

            broadcast(new \App\Events\FriendActionResponse($user, 'friend.list', $friendsData));
        } catch (\Throwable $e) {
            $user = User::find($userId);
            broadcast(new \App\Events\FriendActionResponse(
                $user,
                'friend.list',
                [
                    'success' => false,
                    'message' => 'خطا در دریافت لیست دوستان: ' . $e->getMessage()
                ]
            ));
            return;
        }
    }

    protected function handleFriendRequest($data)
    {
        try {
            $fromUser = User::find($data['from_user']);
            $target   = User::where('username', $data['username'])->first();

            if (!$fromUser || !$target) {
                broadcast(new \App\Events\FriendActionResponse($fromUser, 'friend.request.response', [
                    'success' => false,
                    'message' => 'کاربر یافت نشد'
                ]));
                return;
            }
            if ($fromUser->id === $target->id) {
                broadcast(new \App\Events\FriendActionResponse($fromUser, 'friend.request.response', [
                    'success' => false,
                    'message' => 'امکان ارسال درخواست به خود وجود ندارد'
                ]));
                return;
            }
            $friend = Friend::where(function($q) use ($fromUser, $target) {
                $q->where('user_id', $fromUser->id)->where('friend_id', $target->id);
            })->orWhere(function($q) use ($fromUser, $target) {
                $q->where('user_id', $target->id)->where('friend_id', $fromUser->id);
            })->first();

            if ($friend) {
                if ($friend->status === 'accepted') {
                    broadcast(new \App\Events\FriendActionResponse($fromUser, 'friend.request.response', [
                        'success' => false,
                        'message' => 'شما در حال حاضر با این کاربر دوست هستید'
                    ]));
                    return;
                }
                if ($friend->status === 'pending') {
                    broadcast(new \App\Events\FriendActionResponse($fromUser, 'friend.request.response', [
                        'success' => false,
                        'message' => 'درخواست قبلاً ارسال شده است'
                    ]));
                    return;
                }
                if ($friend->status == 'declined') {
                    $friend->user_id = $fromUser->id;
                    $friend->friend_id = $target->id;
                    $friend->status = 'pending';
                    $friend->save();
                    Cache::forget("user_friends_{$fromUser->id}");
                    Cache::forget("user_friends_{$target->id}");

                    broadcast(new \App\Events\FriendActionResponse($fromUser, 'friend.request.response', [
                        'success' => true,
                        'message' => 'درخواست مجدد ارسال شد'
                    ]));
                    return;
                }
            } else {
                Friend::create([
                    'user_id' => $fromUser->id,
                    'friend_id' => $target->id,
                    'status' => 'pending'
                ]);
                Cache::forget("user_friends_{$fromUser->id}");
                Cache::forget("user_friends_{$target->id}");
        
                broadcast(new \App\Events\FriendActionResponse($fromUser, 'friend.request.response', [
                    'success' => true,
                    'message' => 'درخواست ارسال شد'
                ]));
            }
        } catch (\Throwable $e) {
            $user = isset($data['from_user']) ? $data['from_user'] : null;
            broadcast(new \App\Events\FriendActionResponse(
                User::find($user),
                'friend.request.response',
                [
                    'success' => false,
                    'message' => 'خطا در ارسال درخواست دوستی: ' . $e->getMessage()
                ]
            ));
            return;
        }
    }

    protected function handleFriendAccept($data)
    {
        try {
            $friend = Friend::find($data['id']);
            $userId = $data['user_id'];

            if ($friend && $friend->friend_id == $userId) {
                $friend->status = 'accepted';
                $friend->save();

                Cache::forget("user_friends_{$userId}");
                broadcast(new \App\Events\FriendActionResponse(User::find($userId), 'friend.accept.response', [
                    'success' => true
                ]));
            }
        } catch (\Throwable $e) {
            $user = isset($data['user_id']) ? User::find($data['user_id']) : null;
            broadcast(new \App\Events\FriendActionResponse(
                $user,
                'friend.accept.response',
                [
                    'success' => false,
                    'message' => 'خطا در پذیرش درخواست دوستی: ' . $e->getMessage()
                ]
            ));
            return;
        }
    }

    protected function handleFriendDecline($data)
    {
        try {
            $friend = Friend::find($data['id']);
            $userId = $data['user_id'];

            if ($friend && $friend->friend_id == $userId) {
                $friend->status = 'declined';
                $friend->save();

                Cache::forget("user_friends_{$userId}");
                broadcast(new \App\Events\FriendActionResponse(User::find($userId), 'friend.decline.response', [
                    'success' => true
                ]));
            }
        } catch (\Throwable $e) {
            $user = isset($data['user_id']) ? User::find($data['user_id']) : null;
            broadcast(new \App\Events\FriendActionResponse(
                $user,
                'friend.decline.response',
                [
                    'success' => false,
                    'message' => 'خطا در پذیرش رد درخواست دوستی: ' . $e->getMessage()
                ]
            ));
            return;
        }
    }

    protected function handleFriendRemove($data)
    {
        try {
            $authId  = $data['user_id'];
            $friendId = $data['id'];

            $friend = Friend::where(function($q) use ($authId, $friendId) {
                $q->where('user_id', $authId)->where('friend_id', $friendId);
            })->orWhere(function($q) use ($authId, $friendId) {
                $q->where('friend_id', $authId)->where('user_id', $friendId);
            })->first();

            if ($friend) {
                $friend->delete();
                Message::where(function($q) use ($authId, $friendId) {
                    $q->where('from_user_id', $authId)->where('to_user_id', $friendId);
                })->orWhere(function($q) use ($authId, $friendId) {
                    $q->where('from_user_id', $friendId)->where('to_user_id', $authId);
                })->delete();

                Cache::forget("user_friends_{$authId}");
                broadcast(new \App\Events\FriendActionResponse(User::find($authId), 'friend.remove.response', [
                    'success' => true
                ]));
            }
        } catch (\Throwable $e) {
            $user = isset($data['user_id']) ? User::find($data['user_id']) : null;
            broadcast(new \App\Events\FriendActionResponse(
                $user,
                'friend.remove.response',
                [
                    'success' => false,
                    'message' => 'خطا در حذف دوستی: ' . $e->getMessage()
                ]
            ));
            return;
        }
    }

    protected function handleFriendFavorite($data)
    {
        try {
            $authId = $data['user_id'];
            $targetId = $data['id'];

            $friend = Friend::where(function($q) use ($authId, $targetId) {
                $q->where('user_id', $authId)->where('friend_id', $targetId);
            })->orWhere(function($q) use ($authId, $targetId) {
                $q->where('friend_id', $authId)->where('user_id', $targetId);
            })->first();

            if (!$friend) return;

            $favoriteCount = Friend::where(function($q) use ($authId) {
                $q->where('user_id', $authId)->orWhere('friend_id', $authId);
            })->where('status', 'accepted')->where('is_favorite', true)->count();

            if (!$friend->is_favorite && $favoriteCount >= 5) {
                broadcast(new \App\Events\FriendActionResponse(User::find($authId), 'friend.favorite.response', [
                    'success' => false,
                    'error' => 'حداکثر ۵ دوست محبوب مجاز است'
                ]));
                return;
            }

            $friend->is_favorite = !$friend->is_favorite;
            $friend->save();

            Cache::forget("user_friends_{$authId}");

            broadcast(new \App\Events\FriendActionResponse(User::find($authId), 'friend.favorite.response', [
                'success' => true
            ]));
        } catch (\Throwable $e) {
            $user = isset($data['user_id']) ? User::find($data['user_id']) : null;
            broadcast(new \App\Events\FriendActionResponse(
                $user,
                'friend.favorite.response',
                [
                    'success' => false,
                    'message' => 'خطا در تغییر وضعیت محبوبیت: ' . $e->getMessage()
                ]
            ));
        }
    }
}
