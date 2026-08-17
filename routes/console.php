<?php

use Illuminate\Support\Facades\Schedule;

Schedule::command('queue:work --stop-when-empty --tries=3 --timeout=50 --sleep=2 --max-jobs=10')
    ->everyMinute()
    ->withoutOverlapping();

Schedule::command('irs:sync-990n')
    ->weeklyOn(1, '10:00') // Mondays 10am
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

