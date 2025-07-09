<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\UserSession;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Session;
use App\Events\SessionListUpdated;
use App\Events\SessionDeleteResponse;

class SessionListener extends Command
{
    protected $signature = 'session:listen';
    protected $description = 'Listen for session events via Redis pub/sub';

    public function handle()
    {
        $this->info("Starting Session Listener...");

        try {
            $redis = new \Redis();
            $connected = $redis->connect(env('REDIS_HOST'), env('REDIS_PORT'), 5); // 5 ثانیه timeout

            if (!$connected) {
                $this->error("Could not connect to Redis at " . env('REDIS_HOST') . ":" . env('REDIS_PORT'));
                return;
            }

            if (!$redis->auth(env('REDIS_PASSWORD'))) {
                $this->error("Redis AUTH failed.");
                return;
            }

            $this->info("Connected to Redis, waiting for session events...");

            $redis->psubscribe(['session:*'], function ($redis, $pattern, $channel, $message) {
                $this->info("[REDIS] Received on $channel: $message");
                $data = json_decode($message, true);

                if (!isset($data['user_id'])) {
                    return;
                }

                if (str_ends_with($channel, 'session:list')) {
                    $this->handleSessionList($data['user_id']);
                } elseif (str_ends_with($channel, 'session:delete') && isset($data['session_id'])) {
                    $this->handleSessionDelete($data['user_id'], $data['session_id']);
                } else {
                    $this->warn("[REDIS] ⚠️ شرط نامعتبر: $channel یا شناسه ناقص");
                }

            });

        } catch (\RedisException $e) {
            $this->error("Redis Exception: " . $e->getMessage());
        } catch (\Throwable $e) {
            $this->error("General Exception: " . $e->getMessage());
        }
    }

    protected function handleSessionList($userId)
    {
        $cacheKey = "user_sessions_{$userId}";

        $sessions = Cache::remember($cacheKey, now()->addMinutes(30), function () use ($userId) {
            return UserSession::where('user_id', $userId)
                ->select('id', 'device', 'ip_address', 'is_online', 'last_activity')
                ->get();
        });
        broadcast(new SessionListUpdated($userId, $sessions));
    }

    protected function handleSessionDelete($userId, $sessionId)
    {
        try {
            $session = UserSession::where('id', $sessionId)
                ->where('user_id', $userId)
                ->first();

            if (!$session) {
                broadcast(new SessionDeleteResponse($userId, false, "سشن پیدا نشد."));
                return;
            }

            if (!$session->session_id || strlen($session->session_id) < 10) {
                broadcast(new SessionDeleteResponse($userId, false, "شناسه سشن معتبر نیست."));
                return;
            }

            if ($session->created_at->diffInHours(now()) < 24) {
                broadcast(new SessionDeleteResponse($userId, false, "این سشن کمتر از ۲۴ ساعت ساخته شده و قابل حذف نیست."));
                return;
            }

            Session::getHandler()->destroy($session->session_id);
            $session->delete();
            Cache::forget("user_sessions_{$userId}");

            broadcast(new SessionDeleteResponse($userId, true, "سشن با موفقیت حذف شد."));
            $this->handleSessionList($userId);
        } catch (\Throwable $e) {
            broadcast(new SessionDeleteResponse($userId, false, 'خطا در حذف سشن: ' . $e->getMessage()));
            return;
        }
    }
}
