<?php

namespace App\Services\Installer\DTOs;

final class DbConfigDTO
{
    public function __construct(
        public string $driver,
        public string $host,
        public int    $port,
        public string $database,
        public string $username,
        public string $password,
    ) {}
}