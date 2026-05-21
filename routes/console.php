<?php

declare(strict_types=1);

use App\Console\Commands\CheckCarDocumentExpiry;
use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

/*
 * Daily document-expiry sweep — runs at 06:00 Africa/Cairo per spec §9.3.
 * Production cron needs `* * * * * php artisan schedule:run` per cPanel constraint
 * documented in DEPLOY.md.
 */
Schedule::command(CheckCarDocumentExpiry::class)
    ->dailyAt('06:00')
    ->timezone('Africa/Cairo')
    ->name('documents:check-expiry')
    ->onOneServer();
