<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

/*
|--------------------------------------------------------------------------
| Custom Artisan Commands
|--------------------------------------------------------------------------
*/

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

/*
|--------------------------------------------------------------------------
| Scheduler
|--------------------------------------------------------------------------
|
| Dispatch expired bookings every minute.
| Uses queue-based scalable expiration architecture.
|
*/

Schedule::command('bookings:dispatch-expired')
    ->everyMinute()
    ->withoutOverlapping()
    ->runInBackground()
    ->onOneServer();