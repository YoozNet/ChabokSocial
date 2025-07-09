<?php

namespace App\Listeners;

use App\Models\UserSession;
use Illuminate\Auth\Events\Login;

class LogUserSession
{
    /**
     * Handle the event.
     */
    public function handle(Login $event): void
    {
        $user = $event->user;

        session()->start();  

        $ip = request()->ip();
        $agent = request()->userAgent();
        $device = $this->detectDevice($agent);

        UserSession::updateOrCreate(
            [
                'user_id'    => $user->id,
                'ip_address' => $ip,
                'agent'      => $agent,
            ],
            [
                'device'       => $device,
                'is_online'    => true,
                'last_activity'=> now(),
                'session_id'   => session()->getId(),
            ]
        );
    }


    /**
     * تشخیص نوع دستگاه
     */
    protected function detectDevice($agent)
    {
        $agent = strtolower($agent);
        if (str_contains($agent, 'android')) return 'Android';
        if (str_contains($agent, 'iphone')) return 'iPhone';
        if (str_contains($agent, 'windows')) return 'Windows';
        if (str_contains($agent, 'mac')) return 'Mac';
        return 'Unknown';
    }
}
