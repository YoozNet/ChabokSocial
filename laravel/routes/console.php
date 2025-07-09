<?php

use Illuminate\Support\Facades\Schedule;

Schedule::command('my:check-online')->everyMinute();