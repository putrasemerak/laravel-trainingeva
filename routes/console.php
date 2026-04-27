<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;

use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

// Automatically send evaluation reminders to supervisors every day at 8:00 AM
Schedule::command('evaluations:send-reminders')->dailyAt('08:00');
