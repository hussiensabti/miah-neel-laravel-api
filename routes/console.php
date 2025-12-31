<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

// تشغيل الكرون (لارفيل 12)
Schedule::command('driver:record-daily-stats')
    ->dailyAt('23:50')
    ->withoutOverlapping();