<?php

namespace App\Services\Chat\DTOs;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Collection;
use Illuminate\Support\Carbon;

class ChatContextDTO
{
    public function __construct(
        public ?int $roomId = null,
        public ?string $slug = null,
        public ?string $title = null,
        public ?string $description = null,
        public ?string $password = null,
        public ?string $expiresAt = null,

        public ?string $senderNickname = null,
        public ?string $senderMaskedIp = null,
        public ?string $messageBody = null,

        /** @var Collection<int, UploadedFile>|null */
        public ?Collection $attachments = null,

        public ?string $clientId = null,
        public ?int $afterMessageId = null,
    ) {}
}