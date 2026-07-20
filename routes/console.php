<?php

use Illuminate\Support\Facades\Schedule;

Schedule::command('queue:work --stop-when-empty --tries=3 --timeout=50 --sleep=2 --max-jobs=10')
    ->everyMinute();

Schedule::command('irs:sync-990n')
    ->weeklyOn(1, '06:00') // Mondays 6am
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
