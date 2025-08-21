<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

// Schedule uptime checks every thirty seconds
    Schedule::command('app:check-uptime-command')
        ->everyThirtySeconds()
        ->withoutOverlapping(5)
        ->runInBackground()
        ->onSuccess(function () {
            \Illuminate\Support\Facades\Log::info('Uptime check command completed successfully');
        })
        ->onFailure(function () {
            \Illuminate\Support\Facades\Log::error('Uptime check command failed');
        });

