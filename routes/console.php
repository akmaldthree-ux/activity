<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

// Reminder plan pagi jam 08:30 WIB (UTC+7 = 01:30 UTC)
Schedule::command('reminder:plan')->dailyAt('01:30');

// Reminder report sore jam 17:00 WIB (UTC+7 = 10:00 UTC)
Schedule::command('reminder:report')->dailyAt('10:00');
