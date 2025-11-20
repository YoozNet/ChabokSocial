<?php

namespace App\Services\Installer\DTOs;

final class EnvConfigDTO
{
    public function __construct(
        public string $appName,
        public string $appUrl,
        public string $appEnv,
        public bool   $appDebug,
        public string $cacheDriver,
        public string $sessionDriver,
        public string $queueConnection,
    ) {}
}