<?php

namespace App\Http\Controllers\Admin;

use App\Http\Requests\Admin\AdminChatRoomIndexRequest;
use App\Http\Controllers\Controller;
use App\Services\Chat\ChatMessageService;
use App\Services\Chat\ChatRoomService;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;
use App\Models\ChatRoom;
use Carbon\Carbon;

class AdminChatRoomController extends Controller
{
    public function __construct(
        private ChatRoomService $roomService,
        private ChatMessageService $messageService,
    ) {}

    public function index(AdminChatRoomIndexRequest $request)
    {
        $filters = $request->validatedFilters();
        $perPage = $request->perPage();

        $rooms = $this->roomService->listRoomsForAdmin($filters, $perPage);

        return view('admin.chat-rooms.index', [
            'rooms'   => $rooms,
            'filters' => $filters,
            'perPage' => $perPage,
        ]);
    }

    public function show(ChatRoom $room)
    {
        try {
            $this->roomService->ensureRoomIsAccessible($room);
        } catch (\RuntimeException $e) {
        }
        $lastActivity = $room->messages()->max('created_at');
        $stats = [
            'messages_count' => $room->messages()->count(),
            'files_count'    => $room->messages()->withCount('attachments')->get()->sum('attachments_count'),
            'last_activity'  => $lastActivity ? Carbon::parse($lastActivity) : null,
        ];

        return view('admin.chat-rooms.show', [
            'room'  => $room,
            'stats' => $stats,
        ]);
    }

    public function messages(ChatRoom $room)
    {
        $messages = $this->messageService->getMessagesForAdmin($room, 50);

        return view('admin.chat-rooms.messages', [
            'room'     => $room,
            'messages' => $messages,
        ]);
    }

    public function deactivate(ChatRoom $room)
    {
        $this->roomService->deactivateRoom($room);

        return back()->with('status', 'اتاق غیرفعال شد.');
    }

    public function activate(ChatRoom $room)
    {
        $this->roomService->activateRoom($room);

        return back()->with('status', 'اتاق دوباره فعال شد.');
    }

    public function extendExpire(ChatRoom $room)
    {
        $option = request()->string('expire_option')->toString(); // '15m','1h','1d','1w'
        $this->roomService->extendExpire($room, $option);

        return back()->with('status', 'زمان انقضای اتاق تمدید شد.');
    }

    public function destroy(ChatRoom $room)
    {
        $this->roomService->deleteRoomWithRelations($room);

        return redirect()->route('admin.chat-rooms.index')
            ->with('status', 'اتاق و پیام‌های مربوط حذف شدند.');
    }
}