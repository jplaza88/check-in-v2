<?php

declare(strict_types=1);

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function (): void {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

// Populates Horizon's metrics dashboard (throughput / wait times).
Schedule::command('horizon:snapshot')->everyFiveMinutes();

// Hourly, not daily: "5pm the day before" lands on a different UTC instant for
// each location's timezone, and an hourly sweep also catches up after a missed
// run instead of dropping a day of reminders. Idempotent either way - the
// reminder_sent_at stamp is what actually prevents a second send.
Schedule::command('appointments:send-reminders')->hourly()->withoutOverlapping();
