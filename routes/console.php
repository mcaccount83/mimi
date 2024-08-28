<?php

use Illuminate\Support\Facades\Schedule;

// Run the queue worker every minute
Schedule::command('queue:work --stop-when-empty')->everyMinute();
