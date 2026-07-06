<?php

use Illuminate\Support\Facades\Schedule;

Schedule::command('queue:work --stop-when-empty --tries=3 --timeout=50 --sleep=2 --max-jobs=10')->everyMinute();

Schedule::call(function () {
    \Illuminate\Support\Facades\Log::info('Scheduler heartbeat: ' . now());
})->everyMinute();
