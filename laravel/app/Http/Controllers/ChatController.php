<?php

namespace App\Http\Controllers;

use App\Models\Message;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;

class ChatController extends Controller
{
    public function sendMessage(Request $request)
    {
        try { 
            $request->validate([
                'to_user_id' => 'required|exists:users,id',
                'message' => 'nullable|string',
                'attachment' => 'nullable|image|mimes:jpg,jpeg,png|max:30720',
                'reply_to' => 'nullable|exists:messages,id'
            ]);
    
            $me = auth()->id();
            $friend_id = $request->to_user_id;
    
            $ids = [$me, $friend_id];
            sort($ids);
            $chat_id = "{$ids[0]}_{$ids[1]}";
    
            // گرفتن master key من
            $masterKeyMe = \Cache::get("user_{$me}_master_key");
            if (!$masterKeyMe) {
                return response()->json([
                    'status'  => 'error',
                    'message' => 'master key missing'
                ], 403);
            }
    
            // conversation
            $conversation = \App\Models\Conversation::where('user1_id', $ids[0])
                ->where('user2_id', $ids[1])
                ->first();
    
            if (!$conversation) {
                $chatKeyRaw = random_bytes(32);
    
                $masterKeyFriend = \Cache::get("user_{$friend_id}_master_key");
                if (!$masterKeyFriend) {
                    return response()->json([
                        'status'  => 'error',
                        'message' => 'friend master key missing'
                    ], 403);

                }
    
                $ivMe = substr(hash('sha256', $ids[0],true), 0, 16);
                $ivFriend = substr(hash('sha256', $ids[1],true), 0, 16);
    
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
    
            $chatKeyRaw = openssl_decrypt(
                base64_decode($encryptedChatKey),
                'aes-256-cbc',
                $masterKeyMe,
                OPENSSL_RAW_DATA,
                $iv
            );
    
            if (!$chatKeyRaw) {
                return response()->json([
                    'status'  => 'error',
                    'message' => 'chat key decrypt failed'
                ], 500);

            }
    
            $finalMessage = null;
            if($request->message){
                $ivMsg = random_bytes(16);
                $encrypted = openssl_encrypt($request->message, 'aes-256-cbc', $chatKeyRaw, OPENSSL_RAW_DATA, $ivMsg);
                $finalMessage = base64_encode($ivMsg . $encrypted);
            }
    
            $file = null;
            if ($request->hasFile('attachment')) {
                $rawImage = file_get_contents($request->file('attachment')->getRealPath());
                $compressed = gzcompress($rawImage, 9);
    
                $ivFile = random_bytes(16);
                $encrypted = openssl_encrypt($compressed, 'aes-256-cbc', $chatKeyRaw, OPENSSL_RAW_DATA, $ivFile);
                $data = base64_encode($ivFile . $encrypted);
                $filename = uniqid().'_'.time().'.enc';
                $path = storage_path("app/private/attachments/{$filename}");
                file_put_contents($path, $data);
                $file = 'attachments/'.$filename;
            }
    
            Message::create([
                'from_user_id' => $me,
                'to_user_id' => $friend_id,
                'message' => $finalMessage,
                'attachment' => $file,
                'reply_to' => $request->reply_to
            ]);
    
            \Cache::forget("chat_{$chat_id}_page_1");
    
            return response()->json([
                'status' => 'ok'
            ], 200);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'status'  => 'error',
                'message' => $e->validator->errors()->first()
            ], 422);

        } catch (\Throwable $e) {
            return response()->json([
                'status'  => 'error',
                'message' => 'خطا در ارسال پیام: ' . $e->getMessage()
            ], 500);
        }

    }
    public function downloadAttachment($messageId)
    {
        try { 
            $msg = Message::findOrFail($messageId);
            $me = auth()->id();
    
            $masterKeyMe = \Cache::get("user_{$me}_master_key");
            if (!$masterKeyMe) {
                return response()->json([
                    'status'  => 'error',
                    'message' => 'missing master key'
                ], 403);
            }
    
            $ids = [$msg->from_user_id, $msg->to_user_id];
            sort($ids);
    
            $conversation = \App\Models\Conversation::where('user1_id', $ids[0])
                ->where('user2_id', $ids[1])
                ->first();
    
            if (!$conversation) {
                return response()->json([
                    'status'  => 'error',
                    'message' => 'conversation not found'
                ], 404);

            }
    
            $currentUserIsFirst = ($me == $ids[0]);
            $ivSourceId = $currentUserIsFirst ? $ids[0] : $ids[1];
            $iv = substr(hash('sha256', $ivSourceId, true), 0, 16);
            $encryptedChatKey = $currentUserIsFirst ? $conversation->chat_key_for_user1 : $conversation->chat_key_for_user2;
    
            $chatKeyRaw = openssl_decrypt(
                base64_decode($encryptedChatKey),
                'aes-256-cbc',
                $masterKeyMe,
                OPENSSL_RAW_DATA,
                $iv
            );
    
            if (!$chatKeyRaw) {
                return response()->json([
                    'status'  => 'error',
                    'message' => 'cannot decrypt chat key'
                ], 500);

            }
    
            $path = storage_path('app/private/' . $msg->attachment);
            if (!file_exists($path)) {
                return response()->json([
                    'status'  => 'error',
                    'message' => 'file not found'
                ], 404);

            }
    
            $data = file_get_contents($path);
            $raw = base64_decode($data);
            if ($raw === false || strlen($raw) < 16) {
                return response()->json([
                    'status'  => 'error',
                    'message' => 'invalid encrypted file'
                ], 500);

            }
    
            $ivFile = substr($raw, 0, 16);
            $ciphertext = substr($raw, 16);
    
            $decrypted = openssl_decrypt(
                $ciphertext,
                'aes-256-cbc',
                $chatKeyRaw,
                OPENSSL_RAW_DATA,
                $ivFile
            );
    
            if ($decrypted === false) {
                return response()->json([
                    'status'  => 'error',
                    'message' => 'cannot decrypt attachment'
                ], 500);

            }
            $original = gzuncompress($decrypted);
            if ($original === false) {
                return response()->json([
                    'status'  => 'error',
                    'message' => 'failed to uncompress attachment'
                ], 500);

            }
            return response($original, 200, [
                'Content-Type' => 'application/octet-stream',
                'Content-Disposition' => 'attachment; filename="attachment.jpg"',
                'X-Content-Type-Options' => 'nosniff'
            ]);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'status'  => 'error',
                'message' => 'message not found'
            ], 404);

        } catch (\Throwable $e) {
            return response()->json([
                'status'  => 'error',
                'message' => 'internal server error'
            ], 500);
        }

    }
    public function updateMessage(Request $request, $id)
    {
        try {
            $request->validate([
                'message' => 'required|string',
            ]);
    
            $user = auth()->user();
    
            $msg = Message::where('id', $id)
                ->where('from_user_id', $user->id)
                ->firstOrFail();
    
            // قفل زمانی
            $diff = now()->diffInHours($msg->created_at);
            if ($diff > 72) {
                return response()->json([
                    'status'  => 'error',
                    'message' => 'ویرایش پیام پس از ۷۲ ساعت مجاز نیست.'
                ], 403);
            }
            $masterKeyMe = \Cache::get("user_{$user->id}_master_key");
            if (!$masterKeyMe) {
                return response()->json([
                    'status'  => 'error',
                    'message' => 'master key missing'
                ], 403);
            }
            $ids = [$msg->from_user_id, $msg->to_user_id];
            sort($ids);
    
            $conversation = \App\Models\Conversation::where('user1_id', $ids[0])
                ->where('user2_id', $ids[1])
                ->first();
    
            if (!$conversation) {
                return response()->json([
                    'status'  => 'error',
                    'message' => 'conversation not found'
                ], 404);
            }
            $currentUserIsFirst = ($user->id == $ids[0]);
            $ivSourceId = $currentUserIsFirst ? $ids[0] : $ids[1];
            $iv = substr(hash('sha256', $ivSourceId, true), 0, 16);
            
            $encryptedChatKey = $currentUserIsFirst ? $conversation->chat_key_for_user1 : $conversation->chat_key_for_user2;
    
            $chatKeyRaw = openssl_decrypt(
                base64_decode($encryptedChatKey),
                'aes-256-cbc',
                $masterKeyMe,
                OPENSSL_RAW_DATA,
                $iv
            );
    
            if (!$chatKeyRaw) {
                return response()->json([
                    'status'  => 'error',
                    'message' => 'chat key decrypt failed'
                ], 500);
            }
            $ivMsg = random_bytes(16);
            $ciphertext = openssl_encrypt($request->message, 'aes-256-cbc', $chatKeyRaw, OPENSSL_RAW_DATA, $ivMsg);
            $final = base64_encode($ivMsg . $ciphertext);
    
            $msg->message = $final;
            $msg->is_edited = 1;
            $msg->updated_at = now();
            $msg->save();
    
            $page = ceil(
                Message::where(function ($q) use ($msg) {
                    $q->where('from_user_id', $msg->from_user_id)
                    ->where('to_user_id', $msg->to_user_id);
                })->orWhere(function ($q) use ($msg) {
                    $q->where('from_user_id', $msg->to_user_id)
                    ->where('to_user_id', $msg->from_user_id);
                })->where('id', '<=', $msg->id)->count() / 20
            );
    
            \Cache::forget("chat_{$ids[0]}_{$ids[1]}_page_" . $page);
    
            return response()->json([
                'status' => 'ok'
            ], 200);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'status'  => 'error',
                'message' => $e->validator->errors()->first()
            ], 422);

        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'status'  => 'error',
                'message' => 'پیام یافت نشد.'
            ], 404);

        } catch (\Throwable $e) {
            return response()->json([
                'status'  => 'error',
                'message' => 'خطا در ویرایش پیام'
            ], 500);
        }
    }
    public function deleteMessage(Request $request)
    {
        try {
            $request->validate([
                'message_id' => 'required|integer|exists:messages,id'
            ]);
            $id = $request->message_id;
            $msg = Message::where('id', $id)->firstOrFail();
            $me = auth()->id();
    
            if ($msg->from_user_id !== $me && $msg->to_user_id !== $me) {
                return response()->json(['error' => 'Unauthorized'], 403);
            }
            $msg->delete();
    
            return response()->json(['success' => true]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'status'  => 'error',
                'message' => $e->validator->errors()->first()
            ], 422);

        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'status'  => 'error',
                'message' => 'پیام یافت نشد'
            ], 404);

        } catch (\Throwable $e) {
            return response()->json([
                'status'  => 'error',
                'message' => 'خطا در حذف پیام'
            ], 500);
        }
    }
    public function clearConversation(Request $request)
    {
        try {
            $me = auth()->id();
    
            $request->validate([
                'friend_id' => 'required|numeric|exists:users,id'
            ]);
    
            $friend_id = $request->friend_id;
    
            $isFriend = \App\Models\Friend::where(function ($q) use ($me, $friend_id) {
                $q->where('user_id', $me)->where('friend_id', $friend_id);
            })->orWhere(function ($q) use ($me, $friend_id) {
                $q->where('user_id', $friend_id)->where('friend_id', $me);
            })->where('status', 'accepted')->exists();
    
            if (!$isFriend && $friend_id != $me) {
                if (! $isFriend && $friend_id !== $me) {
                    return response()->json([
                        'status'  => 'error',
                        'message' => 'مجوز دسترسی ندارید'
                    ], 403);
                }
            }
    
            $messages = Message::where(function ($q) use ($me, $friend_id) {
                $q->where('from_user_id', $me)->where('to_user_id', $friend_id);
            })->orWhere(function ($q) use ($me, $friend_id) {
                $q->where('from_user_id', $friend_id)->where('to_user_id', $me);
            })->get();
    
            foreach ($messages as $msg) {
                if ($msg->attachment) {
                    $fullPath = storage_path("app/private/{$msg->attachment}");
                    if (file_exists($fullPath)) {
                        unlink($fullPath);
                    }
                }
            }
    
            Message::whereIn('id', $messages->pluck('id'))->delete();
    
            $totalPages = ceil($messages->count() / 20);
            $ids = [$me, $friend_id];
            sort($ids);
            $chat_id = "{$ids[0]}_{$ids[1]}";
    
            for ($page = 1; $page <= $totalPages; $page++) {
                \Cache::forget("chat_{$chat_id}_page_{$page}");
            }
    
            return response()->json([
                'status' => 'ok'
            ], 200);

        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'status'  => 'error',
                'message' => $e->validator->errors()->first()
            ], 422);

        } catch (\Throwable $e) {
            return response()->json([
                'status'  => 'error',
                'message' => 'خطا در پاک‌سازی گفتگو'
            ], 500);
        }
    }
}