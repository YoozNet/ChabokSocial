<?php

namespace App\Http\Middleware;

use Symfony\Component\HttpFoundation\Response;
use Illuminate\Http\Request;
use Closure;

class InstallGuard
{
    public function handle(Request $request, Closure $next): Response
    {
        $installed = (bool) config('app.installed');
        $isInstallRoute = str_starts_with($request->path(), 'install');

        if (!$installed && !$isInstallRoute) {
            return redirect()->to('/install');
        }

        if ($installed && $isInstallRoute) {
            return redirect()->to('/');
        }

        return $next($request);
    }
}