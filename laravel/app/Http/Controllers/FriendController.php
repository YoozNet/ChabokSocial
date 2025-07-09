<?php

namespace App\Http\Controllers;

use App\Models\Friend;
use App\Models\Message;
use App\Models\User;
use Illuminate\Http\Request;

class FriendController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();
        $cacheKey = "user_friends_{$user->id}";
         $friendsData = \Cache::remember($cacheKey, now()->addSeconds(30), function() use ($user) {
             $pendingRequests = Friend::with('user')
                 ->where('friend_id', $user->id)
                 ->where('status', 'pending')
                 ->orderBy('created_at', 'desc')
                 ->take(5)
                 ->get();
                 $acceptedFriends = Friend::with(['user', 'friend'])
                     ->where('status', 'accepted')
                     ->where(function($q) use ($user) {
                         $q->where('user_id', $user->id)
                         ->orWhere('friend_id', $user->id);
                     })
                     ->get()
                     ->map(function($row) use ($user) {
                         $friend = $row->user_id == $user->id ? $row->friend : $row->user;
         
                         $unreadCount = Message::where('from_user_id', $friend->id)
                             ->where('to_user_id', $user->id)
                             ->where('is_read', 0)
                             ->count();
         
                         $friend->unread_count = $unreadCount;

                         $friend->is_favorite = $row->is_favorite ?? false;
         
                         return $friend;
                     })
                     ->sortByDesc(function($friend) {
                         return $friend->unread_count;
                     })
                     ->sortByDesc(function($friend) {
                         return $friend->is_online; // آنلاین‌ها بالا
                     })
                     ->sortByDesc(function($friend) {
                         return $friend->is_favorite; // محبوب‌ها اولویت اول
                     })
                     ->values();
                     $sentRequests = Friend::with('friend')
                         ->where('user_id', $user->id)
                         ->where('status', '!=', 'accepted')
                         ->orderBy('created_at', 'desc')
                         ->take(3)
                         ->get();
                return [
                    'pending' => $pendingRequests,
                    'friends' => $acceptedFriends,
                    'sent' => $sentRequests,
                ];     
         });

        return response()->json($friendsData);
    }

    public function sendRequest(Request $request)
    {
        $request->validate([
            'friend_id' => 'required|exists:users,id'
        ]);

        $authId = $request->user()->id;
        $targetId = $request->friend_id;

        $friend = Friend::where(function($q) use ($authId, $targetId) {
            $q->where('user_id', $authId)->where('friend_id', $targetId);
        })->orWhere(function($q) use ($authId, $targetId) {
            $q->where('user_id', $targetId)->where('friend_id', $authId);
        })->first();

        if ($friend) {
            if ($friend->status == 'declined') {
                $friend->user_id = $authId;
                $friend->friend_id = $targetId;
                $friend->status = 'pending';
                $friend->save();

                $targetUser = User::find($targetId);
                $targetUser->notifications()->create([
                    'type' => 'new_friend',
                    'message' => 'درخواست دوستی از طرف ' . $request->user()->name . ' برای شما ارسال شد 👥',
                    'read' => false,
                ]);
                \Cache::forget("user_friends_{$request->user()->id}");
                return response()->json(['success'=>true, 'message'=>'درخواست مجدد ارسال شد']);
            }
            if ($friend->status == 'pending') {
                return response()->json(['error'=>'درخواستی در جریان است'], 400);
            }
            if ($friend->status == 'accepted') {
                return response()->json(['error'=>'شما دوست هستید'], 400);
            }
        } else {
            Friend::create([
                'user_id' => $authId,
                'friend_id' => $targetId,
                'status' => 'pending'
            ]);
            $targetUser = User::find($targetId);
            $targetUser->notifications()->create([
                'type' => 'new_friend',
                'message' => 'درخواست دوستی از طرف ' . $request->user()->name . ' برای شما ارسال شد 👥',
                'read' => false,
            ]);

        }
        \Cache::forget("user_friends_{$request->user()->id}");
        return response()->json(['success'=>true]);
    }

    public function acceptRequest(Request $request)
    {
        $request->validate([
            'id' => 'required|exists:friends,id'
        ]);

        $friend = Friend::find($request->id);
        if ($friend->friend_id == $request->user()->id) {
            $friend->status = 'accepted';
            $friend->save();
            \Cache::forget("user_friends_{$request->user()->id}");
            return response()->json(['success'=>true]);
        }
        return response()->json(['error'=>'unauthorized'],403);
    }

    public function declineRequest(Request $request)
    {
        $request->validate([
            'id' => 'required|exists:friends,id'
        ]);

        $friend = Friend::find($request->id);
        if ($friend->friend_id == $request->user()->id) {
            $friend->status = 'declined';
            $friend->save();
            \Cache::forget("user_friends_{$request->user()->id}");
            return response()->json(['success'=>true]);
        }
        return response()->json(['error'=>'unauthorized'],403);
    }

    public function removeFriend(Request $request)
    {
        $friendId = $request->input('id'); 
        $friend = Friend::where(function($q) use ($friendId) {
            $q->where('user_id', auth()->id())
            ->where('friend_id', $friendId);
        })->orWhere(function($q) use ($friendId) {
            $q->where('friend_id', auth()->id())
            ->where('user_id', $friendId);
        })->first();

        if (!$friend) {
            return response()->json(['error' => 'Friend relation not found'], 404);
        }

        $friend->delete();
        Message::where(function($q) use ($friendId) {
            $q->where('from_user_id', auth()->id())
            ->where('to_user_id', $friendId);
        })->orWhere(function($q) use ($friendId) {
            $q->where('from_user_id', $friendId)
            ->where('to_user_id', auth()->id());
        })->delete();
        \Cache::forget("user_friends_{$request->user()->id}");
        return response()->json(['success' => true]);
    }

    public function status($friendId)
    {
        $friend = User::find($friendId);
        if (!$friend) {
            return response()->json(['ok' => false], 404);
        }

        return response()->json([
            'ok' => true,
            'is_online' => (bool)$friend->is_online,
            'last_seen' => $friend->last_seen
        ]);
    }

    public function toggleFavorite(Request $request)
    {
        $request->validate(['id' => 'required|exists:users,id']);

        $authId = $request->user()->id;
        $targetId = $request->id;

        $friend = Friend::where(function($q) use ($authId, $targetId) {
            $q->where('user_id', $authId)
            ->where('friend_id', $targetId);
        })->orWhere(function($q) use ($authId, $targetId) {
            $q->where('friend_id', $authId)
            ->where('user_id', $targetId);
        })->first();

        if (!$friend) {
            return response()->json(['error'=>'not found'],404);
        }

        // شمارش دوستان محبوب
        $favoriteCount = Friend::where(function($q) use ($authId) {
            $q->where('user_id', $authId)->orWhere('friend_id', $authId);
        })->where('status', 'accepted')->where('is_favorite', true)->count();

        if (!$friend->is_favorite && $favoriteCount >= 5) {
            return response()->json(['error'=>'حداکثر ۵ دوست محبوب مجاز است'],400);
        }

        $friend->is_favorite = !$friend->is_favorite;
        $friend->save();
        \Cache::forget("user_friends_{$request->user()->id}");
        return response()->json(['success'=>true]);
    }

}
