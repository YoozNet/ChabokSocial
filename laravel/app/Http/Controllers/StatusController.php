<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class StatusController extends Controller
{
    public function update(Request $request)
    {
        $user = $request->user();

        $request->validate([
            'online' => 'required|boolean'
        ]);

        $user->is_online = $request->online;
        $user->last_seen = now();
        $user->save();
        
        event(new \App\Events\UserPresenceChanged($user));

        return response()->json(['success' => true]);
    }
}
