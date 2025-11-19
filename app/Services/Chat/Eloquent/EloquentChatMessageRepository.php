<?php

namespace App\Services\Chat\Eloquent;

use App\Services\Chat\Repositories\ChatMessageRepositoryInterface;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Collection;
use App\Models\ChatAttachment;
use App\Models\ChatMessage;

class EloquentChatMessageRepository implements ChatMessageRepositoryInterface
{
    public function createWithAttachments(array $messageData, array $attachmentsData): ChatMessage
    {
        return DB::transaction(function () use ($messageData, $attachmentsData): ChatMessage {
            /** @var ChatMessage $message */
            $message = ChatMessage::create($messageData);

            foreach ($attachmentsData as $attachment) {
                $attachment['chat_message_id'] = $message->id;
                ChatAttachment::create($attachment);
            }

            return $message;
        });
    }

    public function getMessagesAfter(int $roomId, ?int $afterId = null): Collection
    {
        $query = ChatMessage::with('attachments')
            ->where('chat_room_id', $roomId)
            ->orderBy('id');

        if ($afterId !== null) {
            $query->where('id', '>', $afterId);
        }

        return $query->get();
    }
}