<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Console\Scheduling\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

// Schedule FetchEducationNewsJob to run every hour
app(Schedule::class)->job(\App\Jobs\FetchEducationNewsJob::class)->hourly();

