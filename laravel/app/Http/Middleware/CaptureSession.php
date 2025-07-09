<?php

namespace App\Http\Middleware;

use Closure;
use App\Models\UserSession;

class CaptureSession
{
    public function handle($request, Closure $next)
    {
        $response = $next($request);

        if (auth()->check()) {

            if (! $request->hasSession()) {
                try {
                    $request->setLaravelSession(app('session')->driver());
                    $request->getSession()->start();
                } catch (\Throwable $e) {
                    logger("ERROR setting laravel session: " . $e->getMessage());
                }
            }

            $user = auth()->user();
            $ip = $request->ip();
            $agent = $request->userAgent();
            $device = $this->detectDevice($agent);
            $session_id = $request->getSession()->getId();
            UserSession::updateOrCreate(
                [
                    'user_id'    => $user->id,
                    'ip_address' => $ip,
                    'agent'      => $agent,
                ],
                [
                    'device'        => $device,
                    'is_online'     => true,
                    'last_activity' => now(),
                    'session_id'    => $session_id,
                ]
            );
        }


        return $response;
    }

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
