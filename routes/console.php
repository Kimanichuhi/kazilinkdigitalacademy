<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

/**
 * Shared hosting has no persistent `queue:work` daemon and no supervisor —
 * a single cron line (`* * * * * php artisan schedule:run`) drives
 * everything. This entry processes whatever's queued (booking events,
 * M-Pesa STK pushes, notifications, audit logs) once a minute and exits;
 * `--stop-when-empty` guarantees it never lingers past the next minute's
 * invocation, and `withoutOverlapping` is a second safety net in case a
 * batch genuinely takes longer than 60s.
 */
Schedule::command('queue:work', ['--stop-when-empty', '--max-time=50'])
    ->everyMinute()
    ->withoutOverlapping();
