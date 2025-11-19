<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\RateLimiter;
use App\Http\Requests\PresencePingRequest;
use App\Services\Chat\ChatPresenceService;
use App\Services\Chat\DTOs\ChatContextDTO;
use App\Http\Requests\PostMessageRequest;
use App\Services\Chat\ChatMessageService;
use App\Services\Chat\ChatRoomService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use App\Models\ChatRoom;

class ChatMessageController extends Controller
{
    public function __construct(
        private ChatRoomService $roomService,
        private ChatMessageService $messageService,
        private ChatPresenceService $presenceService,
    ) {}

    private function getRoom(string $slug): ChatRoom
    {
        /** @var ChatRoom $room */
        $room = ChatRoom::where('slug', $slug)->firstOrFail();

        $this->roomService->ensureRoomIsAccessible($room);

        return $room;
    }

    public function index(Request $request, string $slug)
    {
        $room = $this->getRoom($slug);

        $afterId = $request->integer('after');

        $messages = $this->messageService->getMessages($room, $afterId);

        return response()->json([
            'data' => $messages,
        ]);
    }

    public function store(PostMessageRequest $request, string $slug)
    {
        $room = $this->getRoom($slug);

        $clientKey = $this->rateLimiterKey($slug);

        if (RateLimiter::tooManyAttempts($clientKey, 1)) {
            return response()->json([
                'message' => 'خیلی سریع پیام می‌فرستی، ۲ ثانیه صبر کن.',
            ], 429);
        }

        RateLimiter::hit($clientKey, 2); // 2 seconds decay

        $ip = $request->ip();
        $maskedIp = $this->maskIp($ip);

        $dto = new ChatContextDTO(
            roomId: $room->id,
            slug: $slug,
            senderNickname: $request->input('sender_nickname'),
            senderMaskedIp: $maskedIp,
            messageBody: $request->input('body'),
            attachments: collect($request->file('attachments', [])),
        );

        $result = $this->messageService->postMessage($room, $dto);

        return response()->json([
            'data' => $result['message'],
        ]);
    }

    public function ping(PresencePingRequest $request, string $slug)
    {
        $room = $this->getRoom($slug);

        $clientId = $request->string('client_id')->toString();

        $this->presenceService->ping($room->id, $clientId);

        return response()->json(['status' => 'ok']);
    }

    public function activeCount(string $slug)
    {
        $room = $this->getRoom($slug);

        $count = $this->presenceService->countActive($room->id);

        return response()->json(['count' => $count]);
    }

    private function maskIp(?string $ip): ?string
    {
        if ($ip === null) {
            return null;
        }

        if (filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_IPV4)) {
            $parts = explode('.', $ip);
            
            if (count($parts) === 4) {
                $parts[1] = 'x';
                $parts[2] = 'x';
                return implode('.', $parts);
            }
        }
        if (filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_IPV6)) {
            return substr($ip, 0, 8) . '::xxxx';
        }
        return $ip;
    }

    private function rateLimiterKey(string $slug): string
    {
        return 'chat_msg:' . $slug . ':' . request()->ip();
    }
}