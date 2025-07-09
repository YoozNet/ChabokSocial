<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class ChatListener extends Command
{
    protected $signature = 'chat:listen';
    protected $description = 'Listen for chat events via Redis';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info("Chat listener started...");

        try {
            $redis = new \Redis();
            $connected = $redis->connect(env('REDIS_HOST'), env('REDIS_PORT'), 5);

            if (!$connected || !$redis->auth(env('REDIS_PASSWORD'))) {
                $this->error("Redis connection/auth failed");
                return;
            }

            $this->info("Connected to Redis. Listening on chat channels...");

            $redis->psubscribe([
                "chat:list",
                "chat:seen",
                "chat:saved",
            ], function ($redis, $pattern, $channel, $message) {
                try {
                    $data = json_decode($message, true);
                    $this->processMessage($channel, $data);
                } catch (\Throwable $e) {
                    $this->error("ChatListener error: " . $e->getMessage());
                }
            });
        } catch (\Throwable $e) {
            $this->error("Exception: " . $e->getMessage());
        }
    }

    protected function processMessage($channel, $data)
    {
        match ($channel) {
            "chat:list"   => $this->handleList($data),
            "chat:seen"   => $this->handleSeen($data),
            "chat:saved"  => $this->handleSaved($data),
            default       => $this->warn("Unknown channel: $channel"),
        };
    }
    protected function handleList(array $data) 
    {
        try {
            $me = $data['from_user_id'];
            $friend_id = $data['to_user_id'];
            $page = $data['page'] ?? 1;
            $perPage = 30;

            $ids = [$me, $friend_id];
            sort($ids);
            $chat_id = "{$ids[0]}_{$ids[1]}";

            $masterKeyMe = \Cache::get("user_{$me}_master_key");
            
            if (!$masterKeyMe) {
                return broadcast(new \App\Events\ChatActionResponse($me, 'chat.list.response', [
                    'success' => false,
                    'message' => 'master key missing'
                ]));
            }

            $conversation = \App\Models\Conversation::where('user1_id', $ids[0])
                ->where('user2_id', $ids[1])
                ->first();

            if (!$conversation) {
                $chatKeyRaw = random_bytes(32);

                $masterKeyFriend = \Cache::get("user_{$friend_id}_master_key");

                if (!$masterKeyFriend) {
                    return broadcast(new \App\Events\ChatActionResponse($me, 'chat.list.response', [
                        'success' => false,
                        'message' => 'friend master key missing'
                    ]));
                }

                $ivMe = substr(hash('sha256', $ids[0], true), 0, 16);
                $ivFriend = substr(hash('sha256', $ids[1], true), 0, 16);

                $chatKeyForMe = base64_encode(openssl_encrypt($chatKeyRaw, 'aes-256-cbc', $masterKeyMe, OPENSSL_RAW_DATA, $ivMe));
                $chatKeyForFriend = base64_encode(openssl_encrypt($chatKeyRaw, 'aes-256-cbc', $masterKeyFriend, OPENSSL_RAW_DATA, $ivFriend));

                $conversation = \App\Models\Conversation::create([
                    'user1_id' => $ids[0],
                    'user2_id' => $ids[1],
                    'chat_key_for_user1' => $chatKeyForMe,
                    'chat_key_for_user2' => $chatKeyForFriend
                ]);
            }

            $currentUserIsFirst = ($me == $ids[0]);
            $ivSourceId = $currentUserIsFirst ? $ids[0] : $ids[1];
            $iv = substr(hash('sha256', $ivSourceId, true), 0, 16);

            $encryptedChatKey = $currentUserIsFirst ? $conversation->chat_key_for_user1 : $conversation->chat_key_for_user2;

            $decoded = base64_decode($encryptedChatKey);
            $chatKeyRaw = openssl_decrypt(
                $decoded,
                'aes-256-cbc',
                $masterKeyMe,
                OPENSSL_RAW_DATA,
                $iv
            );

            if (!$chatKeyRaw) {
                return broadcast(new \App\Events\ChatActionResponse($me, 'chat.list.response', [
                    'success' => false,
                    'message' => 'chat key decrypt failed'
                ]));
            }
            $totalMessages = \Cache::remember("chat_{$chat_id}_total_messages", 10, function () use ($me, $friend_id) {
                return \App\Models\Message::where(function($q) use($me, $friend_id){
                    $q->where('from_user_id', $me)->where('to_user_id', $friend_id);
                })->orWhere(function($q) use($me, $friend_id){
                    $q->where('from_user_id', $friend_id)->where('to_user_id', $me);
                })->count();
            });
            $totalPages = ceil($totalMessages / $perPage);
            $remainingPages = max(0, $totalPages - $page);

            $messages = \Cache::remember("chat_{$chat_id}_page_{$page}", 10, function () use ($me,$friend_id,$page,$perPage) {
                return \App\Models\Message::with('reply')
                    ->where(function($q) use($me,$friend_id){
                        $q->where('from_user_id',$me)->where('to_user_id',$friend_id);
                    })->orWhere(function($q) use($me,$friend_id){
                        $q->where('from_user_id',$friend_id)->where('to_user_id',$me);
                    })
                    ->orderBy('created_at', 'ASC')
                    ->skip(($page-1)*$perPage)
                    ->take($perPage)
                    ->get();
            });

            $output = $messages->map(function($msg) use ($chatKeyRaw) {
                $plainMessage = null;

                if ($msg->message) {
                    $raw = base64_decode($msg->message);
                    if ($raw && strlen($raw) >= 16) {
                        $iv = substr($raw, 0, 16);
                        $ciphertext = substr($raw, 16);
                        $plainMessage = openssl_decrypt($ciphertext, 'aes-256-cbc', $chatKeyRaw, OPENSSL_RAW_DATA, $iv);
                    }
                }

                $plainReply = null;
                if ($msg->reply && $msg->reply->message) {
                    $rawReply = base64_decode($msg->reply->message);
                    if ($rawReply && strlen($rawReply) >= 16) {
                        $ivR = substr($rawReply, 0, 16);
                        $ctR = substr($rawReply, 16);
                        $plainReply = openssl_decrypt($ctR, 'aes-256-cbc', $chatKeyRaw, OPENSSL_RAW_DATA, $ivR);
                    }
                }

                return [
                    'id' => $msg->id,
                    'from_user_id' => $msg->from_user_id,
                    'to_user_id' => $msg->to_user_id,
                    'message' => $plainMessage,
                    'attachment' => $msg->attachment,
                    'is_read' => $msg->is_read,
                    'reply_to' => $msg->reply_to,
                    'created_at' => $msg->created_at,
                    'updated_at' => $msg->updated_at,
                    'is_edited' => $msg->is_edited,
                    'reply' => $msg->reply ? [
                        'id' => $msg->reply->id,
                        'message' => $plainReply
                    ] : null
                ];
            });

            return broadcast(new \App\Events\ChatActionResponse($me, 'chat.list.response', [
                'success' => true,
                'chat_id' => $chat_id,
                'page' => $page,
                'messages' => $output,
                'remaining_pages' => $remainingPages
            ]));
        } catch (\Throwable $e) {
            return broadcast(new \App\Events\ChatActionResponse($data['from_user_id'], 'chat.list.response', [
                'success' => false,
                'message' => 'خطا در پردازش لیست گفتگو: ' . $e->getMessage()
            ]));
        }
    }
    protected function handleSeen(array $data){
        try {
            $userId = $data['from_user_id'] ?? null;
            $messageIds = $data['message_ids'] ?? [];

            if (!$userId || !is_array($messageIds)) {
                return broadcast(new \App\Events\ChatActionResponse($userId, 'chat.seen.response', [
                    'success' => false,
                    'message' => 'data empty'
                ]));
            }
            \App\Models\Message::whereIn('id', $messageIds)
                ->where('to_user_id', $userId)
                ->update([
                    'is_read' => 1,
                    'seen_at' => now(),
                ]);
            return broadcast(new \App\Events\ChatActionResponse($userId, 'chat.seen.response', [
                'success' => true,
                'message_ids' => $messageIds
            ]));
        } catch (\Throwable $e) {
            return broadcast(new \App\Events\ChatActionResponse($data['from_user_id'], 'chat.seen.response', [
                'success' => false,
                'message' => 'خطا در پردازش لیست گفتگو: ' . $e->getMessage()
            ]));
        }
    }
    protected function handleSaved(array $data){
        try {
            $me = $data['from_user_id'];
            $masterKeyMe = \Cache::get("user_{$me}_master_key");
            if (!$masterKeyMe) {
                return broadcast(new \App\Events\ChatActionResponse($me, 'chat.saved.response', [
                    'success' => false,
                    'message' => 'master key missing'
                ]));
            }
            $conversation = \App\Models\Conversation::where('user1_id', $me)
                ->where('user2_id', $me)
                ->first();
            if (!$conversation) {
                $chatKeyRaw = random_bytes(32);

                // IV باینری
                $ivMe = substr(hash('sha256', $me, true), 0, 16);

                $chatKeyForMe = base64_encode(
                    openssl_encrypt($chatKeyRaw, 'aes-256-cbc', $masterKeyMe, OPENSSL_RAW_DATA, $ivMe)
                );

                $conversation = \App\Models\Conversation::create([
                    'user1_id'           => $me,
                    'user2_id'           => $me,
                    'chat_key_for_user1' => $chatKeyForMe,
                    'chat_key_for_user2' => $chatKeyForMe,
                ]);
            }
            $iv = substr(hash('sha256', $me,true), 0, 16);
            $encryptedChatKey = $conversation->chat_key_for_user1;

            $chatKeyRaw = openssl_decrypt(
                base64_decode($encryptedChatKey),
                'aes-256-cbc',
                $masterKeyMe,
                OPENSSL_RAW_DATA,
                $iv
            );
            if (!$chatKeyRaw) {
                return broadcast(new \App\Events\ChatActionResponse($me, 'chat.saved.response', [
                    'success' => false,
                    'message' => 'chat key decrypt failed'
                ]));
            }
            $messages = \App\Models\Message::where('from_user_id', $me)
                ->where('to_user_id', $me)
                ->orderBy('created_at', 'ASC')
                ->get();

            $output = $messages->map(function($msg) use ($chatKeyRaw) {
                $plain = null;
                if($msg->message){
                    $raw = base64_decode($msg->message);
                    if ($raw && strlen($raw) >= 16) {
                        $iv = substr($raw, 0, 16);
                        $ciphertext = substr($raw, 16);
                        $plain = openssl_decrypt($ciphertext, 'aes-256-cbc', $chatKeyRaw, OPENSSL_RAW_DATA, $iv);
                    }
                }
                return [
                    'id' => $msg->id,
                    'message' => $plain,
                    'attachment' => $msg->attachment,
                    'is_read' => $msg->is_read,
                    'reply_to' => $msg->reply_to,
                    'created_at' => $msg->created_at,
                    'updated_at' => $msg->updated_at,
                    'is_edited' => $msg->is_edited,
                ];
            });
            return broadcast(new \App\Events\ChatActionResponse($me, 'chat.saved.response', [
                'success' => true,
                'messages' => $output
            ]));
        } catch (\Throwable $e) {
            return broadcast(new \App\Events\ChatActionResponse($data['from_user_id'], 'chat.saved.response', [
                'success' => false,
                'message' => 'خطا در پردازش لیست گفتگو: ' . $e->getMessage()
            ]));
        }
    }
}
