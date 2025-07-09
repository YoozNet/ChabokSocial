<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\User;

class HeartbeatListener extends Command
{
    protected $signature = 'heartbeat:listen';
    protected $description = 'Listen for user heartbeat events from Redis';

    public function handle()
    {
        $this->info('Listening for heartbeat...');

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

            $this->info("Connected to Redis, waiting for events...");

            $redis->psubscribe(['heartbeat'], function ($redis, $pattern, $channel, $message) {
                $data = json_decode($message, true);

                if (!isset($data['user_id'])) {
                    return;
                }

                $user = User::find($data['user_id']);
                if ($user) {
                    $user->is_online = $data['online'];
                    $user->last_seen = now();
                    $user->save();

                    broadcast(new \App\Events\UserPresenceChanged($user));
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
}
