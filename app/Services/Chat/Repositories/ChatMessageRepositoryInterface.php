<?php

namespace App\Services\Chat\Repositories;

use App\Models\ChatMessage;
use Illuminate\Contracts\Pagination\Paginator;
use Illuminate\Support\Collection;

interface ChatMessageRepositoryInterface
{
    public function createWithAttachments(array $messageData, array $attachmentsData): ChatMessage;

    /**
     * @return Collection<int, ChatMessage>
     */
    public function getMessagesAfter(int $roomId, ?int $afterId = null): Collection;
}