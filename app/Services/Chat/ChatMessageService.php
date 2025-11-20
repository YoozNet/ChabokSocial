<?php

namespace App\Services\Chat;

use App\Services\Chat\Repositories\ChatMessageRepositoryInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use App\Services\Chat\DTOs\ChatContextDTO;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Encryption\Encrypter;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Collection;
use App\Models\ChatMessage;
use App\Models\ChatRoom;

class ChatMessageService
{
    private const MAX_MESSAGE_LENGTH = 4096;

    public function __construct(
        private ChatMessageRepositoryInterface $messages,
    ) {}

    public function postMessage(ChatRoom $room, ChatContextDTO $dto): array
    {
        if ($dto->messageBody !== null && mb_strlen($dto->messageBody) > self::MAX_MESSAGE_LENGTH) {
            throw new \RuntimeException('Message too long.');
        }
        $encryptedBody = $dto->messageBody;

        if ($room->password && $dto->messageBody !== null) {
            $encryptionKey = hash('sha256', $room->password, true);
            $encrypter = new Encrypter($encryptionKey, 'AES-256-CBC');
            $encryptedBody = $encrypter->encryptString($dto->messageBody);
        }

        $messageData = [
            'chat_room_id'     => $room->id,
            'sender_nickname'  => $dto->senderNickname,
            'sender_masked_ip' => $dto->senderMaskedIp,
            'body'             => $encryptedBody,
        ];

        $attachmentsData = [];

        if ($dto->attachments !== null) {
            foreach ($dto->attachments as $file) {
                $path = $file->store('chat_attachments');

                $type = $this->detectType($file->getMimeType());

                $attachmentsData[] = [
                    'type'          => $type,
                    'original_name' => $file->getClientOriginalName(),
                    'mime_type'     => $file->getMimeType(),
                    'size'          => $file->getSize(),
                    'path'          => $path,
                ];
            }
        }

        $message = $this->messages->createWithAttachments($messageData, $attachmentsData);
        $message->load('attachments');
        $message->body = $this->maybeDecryptMessage($message, $room);
        
        return [
            'message' => $message->load('attachments'),
        ];
    }

    /**
     * @return Collection<int, \App\Models\ChatMessage>
     */
    public function getMessages(ChatRoom $room, ?int $afterId = null): Collection
    {
        $messages = $this->messages->getMessagesAfter($room->id, $afterId);

        return $messages->map(function (ChatMessage $message) use ($room) {
            $message->body = $this->maybeDecryptMessage($message, $room);
            return $message;
        });
    }
    private function maybeDecryptMessage(ChatMessage $message, ChatRoom $room): string
    {
        if (!$room->password || empty($message->body)) {
            return $message->body ?? ''; 
        }

        try {
            $encryptionKey = hash('sha256', $room->password, true);
            $encrypter = new Encrypter($encryptionKey, 'AES-256-CBC');

            return $encrypter->decryptString($message->body);
        } catch (\Exception $e) {
            return '[پیام رمزگذاری شده نامعتبر]';
        }
    }
    private function detectType(string $mime): string
    {
        if (str_starts_with($mime, 'image/')) {
            return 'image';
        }

        if (str_starts_with($mime, 'video/')) {
            return 'video';
        }

        return 'file';
    }
    // ---------------------------------------------------------- ADMIN
    public function getMessagesForAdmin(ChatRoom $room, int $perPage = 50): LengthAwarePaginator
    {
        $query = ChatMessage::query()
            ->with('attachments')
            ->where('chat_room_id', $room->id)
            ->orderByDesc('created_at');

        $paginator = $query->paginate($perPage)->withQueryString();

        $paginator->getCollection()->transform(function (ChatMessage $message) use ($room) {
            $message->body = $this->maybeDecryptMessage($message, $room);
            return $message;
        });

        return $paginator;
    }
}