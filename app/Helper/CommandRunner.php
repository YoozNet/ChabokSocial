<?php

namespace App\Helper;

use Illuminate\Support\Facades\Artisan;

final class CommandRunner
{
    public function call(string $command, array $parameters = []): int
    {
        return Artisan::call($command, $parameters);
    }
}