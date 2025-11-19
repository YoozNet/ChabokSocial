<?php

namespace App\Http\Controllers;

use App\Http\Requests\CheckRoomPasswordRequest;
use App\Http\Requests\SetRoomNicknameRequest;
use App\Http\Requests\CreateChatRoomRequest;
use App\Services\Chat\DTOs\ChatContextDTO;
use Illuminate\Support\Facades\Session;
use App\Services\Chat\ChatRoomService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use App\Models\ChatRoom;

class ChatRoomController extends Controller
{
    public function __construct(
        private ChatRoomService $service,
    ) {}

    public function createForm()
    {
        return view('chat.create-room');
    }

    public function store(CreateChatRoomRequest $request)
    {
        $dto = new ChatContextDTO(
            slug: $request->input('slug'),
            title: $request->input('title'),
            description: $request->input('description'),
            password: $request->input('password'),
            expiresAt: $request->input('expire'),
        );
        try {
            $room = $this->service->createRoom($dto);
        } catch (\RuntimeException | \InvalidArgumentException $e) {
            return back()->withErrors(['error' => $e->getMessage()]);
        }

        if (!empty($room->password)) {
            return redirect()->route('chat.password.form', $room->slug);
        }
        
        return redirect()->route('chat.nickname.form', $room->slug);
    }
    public function joinRoom(Request $request)
    {
        $slug = $request->input('slug_search');

        if (empty($slug)) {
            return back()->withErrors(['error' => 'لطفاً شناسه اتاق را وارد کنید.']);
        }
        
        return redirect()->route('chat.show', ['slug' => $slug]);
    }
    public function show(string $slug, Request $request)
    {
        /** @var ChatRoom $room */
        $room = ChatRoom::where('slug', $slug)->firstOrFail();

        try {
            $this->service->ensureRoomIsAccessible($room);
        } catch (\RuntimeException $e) {
            return $this->redirectToPreviousOrRoot($e->getMessage());
        }

        $accessKey   = $this->sessionKey($slug);
        $nicknameKey = $this->nicknameKey($slug);

        if (!empty($room->password)) {
            if (!Session::get($accessKey, false)) {
                return redirect()->route('chat.password.form', $slug);
            }

            if (!Session::has($nicknameKey)) {
                return redirect()->route('chat.password.form', $slug);
            }
        } else {
            if (!Session::has($nicknameKey)) {
                return redirect()->route('chat.nickname.form', $slug);
            }
        }

        return view('chat.room', [
            'room'     => $room,
            'nickname' => Session::get($nicknameKey),
        ]);
    }

    public function passwordForm(string $slug)
    {
        $room = ChatRoom::where('slug', $slug)->firstOrFail();
        try {
            $this->service->ensureRoomIsAccessible($room);
        } catch (\RuntimeException $e) {
            return $this->redirectToPreviousOrRoot($e->getMessage());
        }
        return view('chat.password', [
            'room' => $room,
        ]);
    }

    public function checkPassword(CheckRoomPasswordRequest $request, string $slug): RedirectResponse
    {
        $room = ChatRoom::where('slug', $slug)->firstOrFail();

        try {
            $this->service->verifyPassword($room, $request->string('password')->toString());

        } catch (\RuntimeException $e) {
            return back()->withErrors(['error' => $e->getMessage()]);
        }

        Session::put($this->sessionKey($slug), true);
        $nickname = trim((string) $request->input('nickname'));

        if ($nickname === '') {
            $nickname = 'مهمان';
        }

        Session::put($this->nicknameKey($slug), $nickname);
        return redirect()->route('chat.show', $slug);
    }
    public function nicknameForm(string $slug)
    {
        $room = ChatRoom::where('slug', $slug)->firstOrFail();
        try {
            $this->service->ensureRoomIsAccessible($room);
        } catch (\RuntimeException $e) {
            return $this->redirectToPreviousOrRoot($e->getMessage());
        }
        return view('chat.nickname', [
            'room' => $room,
        ]);
    }

    public function saveNickname(SetRoomNicknameRequest $request, string $slug): RedirectResponse
    {
        $room = ChatRoom::where('slug', $slug)->firstOrFail();

        try {
            $this->service->ensureRoomIsAccessible($room);
        } catch (\RuntimeException $e) {
            return back()->withErrors(['error' => $e->getMessage()]);
        }

        Session::put($this->nicknameKey($slug), $request->string('nickname')->toString());

        if (empty($room->password)) {
            Session::put($this->sessionKey($slug), true);
        }

        return redirect()->route('chat.show', $slug);
    }
    private function sessionKey(string $slug): string
    {
        return 'chat_room_access.' . $slug;
    }
    private function nicknameKey(string $slug): string
    {
        return 'chat_room_nickname.' . $slug;
    }
    private function redirectToPreviousOrRoot(string $message): RedirectResponse
    {
        if (url()->previous() !== url()->current() && url()->previous() !== url()->to('/')) {
            return back()->withErrors(['error' => $message]);
        }
        
        return redirect()->route('chat.create-form')->withErrors(['error' => $message]);
    }
}