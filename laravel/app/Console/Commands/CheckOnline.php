<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class CheckOnline extends Command
{
    protected $signature = 'my:check-online';
    protected $description = 'Set users offline if heartbeat is expired';

    public function handle()
    {
        try {
            $count = DB::table('users')
                ->where('is_online', 1)
                ->where('last_seen', '<', now()->subSeconds(15))
                ->update(['is_online' => 0]);

            $this->info("$count users set to offline");
        } catch (\Throwable $e) {
            $this->error('CheckOnline command failed: ' . $e->getMessage());
            return 1; 
        }
        return 0;
        
    }
}
