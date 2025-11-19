<?php

namespace App\Services\Chat;

use App\Services\Chat\Repositories\ChatRoomRepositoryInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use App\Services\Chat\DTOs\ChatContextDTO;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use App\Models\ChatRoom;
use Carbon\Carbon;

class ChatRoomService
{
    public function __construct(
        private ChatRoomRepositoryInterface $rooms,
    ) {}

    public function createRoom(ChatContextDTO $dto): ChatRoom
    {
        $slug = $dto->slug ?? $this->generateSlug();
        
        $this->ensureSlugIsValid($slug);

        if ($this->rooms->existsBySlug($slug)) {
            throw new \RuntimeException('این شناسه از قبل موجود است');
        }

        $expiresAt = $this->calculateExpiry($dto);

        return $this->rooms->create([
            'slug'        => $slug,
            'title'       => $dto->title,
            'description' => $dto->description,
            'password'    => $dto->password, // ❗ plain text
            'status'      => 'active',
            'expires_at'  => $expiresAt,
        ]);
    }

    public function ensureRoomIsAccessible(ChatRoom $room)
    {
        if ($room->status !== 'active') {
            throw new \RuntimeException('اتاق فعال نیست');
        }
        if ($room->expires_at && $room->expires_at->isPast()) {
            throw new \RuntimeException('اتاق منقضی شده');
        }
    }

    public function verifyPassword(ChatRoom $room, ?string $password): void
    {
        if ($room->password === null) {
            return;
        }

        if ($password === null || $password !== $room->password) {
            throw new \RuntimeException('پسورد وارد شده اشتباه می باشد');
        }
    }

    private function generateSlug(): string
    {
        return Str::random(8);
    }

    private function ensureSlugIsValid(string $slug): void
    {
        if (!preg_match('/^[a-zA-Z0-9-]{3,30}$/', $slug)) {
            throw new \InvalidArgumentException('فرمت ایجاد شناسه اشتباه هست ، لطفا از 0 تا 9 و سپس a تا z به همراه _ انتخاب کنید');
        }

        if ($this->rooms->reservedSlugs()->contains($slug)) {
            throw new \InvalidArgumentException('این شناسه از قبل رزرو شده');
        }
    }

    private function calculateExpiry(ChatContextDTO $dto)
    {
        // assume front sends "expire_option": '15m', '1h', '1d', '1w'
        if ($dto->expiresAt instanceof Carbon) {
            return $dto->expiresAt;
        }

        $option = strtolower($dto->expiresAt);
        $now = now();

        $unit = substr($option, -1);
        $value = (int) substr($option, 0, -1);

        if ($value <= 0) {
            return null;
        }

        switch ($unit) {
            case 'm':
                return $now->addMinutes($value);
            case 'h':
                return $now->addHours($value);
            case 'd':
                return $now->addDays($value);
            case 'w':
                return $now->addDays($value * 7);
            default:
                return null;
        }
    }

    // ---------------------------------------------------------- ADMIN
    public function listRoomsForAdmin(array $filters, int $perPage = 20): LengthAwarePaginator
    {
        $query = ChatRoom::query()
            ->withCount(['messages as messages_count'])
            ->withMax('messages as last_message_at', 'created_at');

        if (!empty($filters['slug'])) {
            $query->where('slug', 'like', '%' . $filters['slug'] . '%');
        }

        if (!empty($filters['title'])) {
            $query->where('title', 'like', '%' . $filters['title'] . '%');
        }

        if (!empty($filters['status']) && $filters['status'] !== 'all') {
            $query->where('status', $filters['status']);
        }

        $query->orderByDesc('created_at');

        return $query->paginate($perPage)->withQueryString();
    }

    public function deactivateRoom(ChatRoom $room): void
    {
        $room->update(['status' => 'inactive']);
    }

    public function activateRoom(ChatRoom $room): void
    {
        $room->update(['status' => 'active']);
    }

    public function extendExpire(ChatRoom $room, string $option): void
    {
        $dto = new ChatContextDTO(
            expiresAt: $option
        );

        $currentExpire = $room->expires_at ?? now();
        $option = strtolower($dto->expiresAt);
        $unit = substr($option, -1);
        $value = (int) substr($option, 0, -1);

        if ($value <= 0) {
            return;
        }

        switch ($unit) {
            case 'm':
                $newExpire = $currentExpire->copy()->addMinutes($value);
                break;
            case 'h':
                $newExpire = $currentExpire->copy()->addHours($value);
                break;
            case 'd':
                $newExpire = $currentExpire->copy()->addDays($value);
                break;
            case 'w':
                $newExpire = $currentExpire->copy()->addDays($value * 7);
                break;
            default:
                $newExpire = $currentExpire;
        }

        $room->update(['expires_at' => $newExpire]);
    }

    public function deleteRoomWithRelations(ChatRoom $room): void
    {
        $room->delete();
    }
}