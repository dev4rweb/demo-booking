<?php

use Illuminate\Support\Facades\Schedule;

Schedule::command('bookings:send-reminders')->hourly();

Schedule::command('queue:work --stop-when-empty --max-time=50')
    ->everyMinute()
    ->when(fn (): bool => config('queue.default') === 'database');
