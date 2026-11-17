<?php

use App\Models\CookieConsent;
use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

// Delete cookie consent records older than config('cookies.log_retention_days').
Schedule::command('model:prune', ['--model' => [CookieConsent::class]])->daily();
