<?php

use Illuminate\Support\Facades\Schedule;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;

Schedule::command('queue:work --stop-when-empty --tries=3 --timeout=50 --sleep=2 --max-jobs=10 --max-time=55')
    ->everyMinute()
    ->withoutOverlapping(5)
    ->after(function () {
        Cache::put('queue_worker_last_run', now());

        if (app()->environment('production')) {
            try {
                Http::timeout(3)->get('https://hc-ping.com/a41dbb95-f986-4c1b-9bc4-5ac435b39d8d');
            } catch (\Throwable $e) {
                // swallow — network hiccups here should never block the scheduler
            }
        }
    });

Schedule::command('irs:sync-990n')
    ->weeklyOn(2, '10:00') // Tuesdays 10am
    ->timezone('America/New_York')
    ->withoutOverlapping();

Schedule::command('reminders:reregistration')
    ->monthlyOn(1, '08:00')
    ->timezone('America/New_York')
    ->withoutOverlapping();

Schedule::command('reminders:reregistration-late')
    ->monthlyOn(10, '08:00')
    ->timezone('America/New_York')
    ->withoutOverlapping();

Schedule::command('moms:reset-fiscal-year')
    ->yearlyOn(7, 1, '00:00')
    ->timezone('America/New_York')
    ->withoutOverlapping();

Schedule::command('moms:reset-report-year')
    ->yearlyOn(1, 1, '00:00')
    ->timezone('America/New_York')
    ->withoutOverlapping();

Schedule::command('moms:subscribe-lists')
    ->yearlyOn(8, 1, '00:00')
    ->timezone('America/New_York')
    ->withoutOverlapping();

Schedule::command('moms:unsubscribe-lists')
    ->yearlyOn(6, 1, '00:00')
    ->timezone('America/New_York')
    ->withoutOverlapping();

Schedule::command('forum:send-daily-digest')
    ->dailyAt('21:00')
    ->timezone('America/New_York')
    ->withoutOverlapping();

Schedule::command('exports:international-to-drive')
    ->monthlyOn(30, '08:00')
    ->withoutOverlapping()
    ->onOneServer();

