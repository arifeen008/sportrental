<?php

use App\Console\Commands\CancelExpiredBookings;
use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan; // <-- เพิ่ม use statement นี้
use Illuminate\Support\Facades\Schedule;
// <-- เพิ่ม use statement ของ Command

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

Schedule::command(CancelExpiredBookings::class)->everyMinute();
