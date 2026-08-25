<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Schedule;

Schedule::command('app:backup')->dailyAt('03:00');
Schedule::command('telegram:set-webhook')->weeklyOn(1, '03:30');
Schedule::command('app:sync-achievements')->dailyAt('04:00');
Schedule::command('cache:clear')->weeklyOn(1, '04:30');
Schedule::command('session:gc')->dailyAt('05:00');
