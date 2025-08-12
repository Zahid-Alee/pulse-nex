<?php

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;


Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');


Artisan::command('websites:check', function () {
    // The logic of the command itself is inside your CheckWebsites class
})->describe('Check websites uptime based on their intervals');

// Schedule the command using the Scheduler instance
app()->booted(function () {
    $schedule = app(Schedule::class);

    // Run every minute but your Website::needsCheck() scope should filter by interval
    $schedule->command('websites:check')->everyMinute();
});
