<?php

declare(strict_types=1);

use App\Console\Commands\CheckCarDocumentExpiry;
use App\Console\Commands\CheckMaintenanceDue;
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

/*
 * Daily maintenance due-sweep per spec §6 Phase 6 — runs at 06:30 Africa/Cairo,
 * 30 minutes after the document sweep so logs are clearly attributable.
 */
Schedule::command(CheckMaintenanceDue::class)
    ->dailyAt('06:30')
    ->timezone('Africa/Cairo')
    ->name('maintenance:check-due')
    ->onOneServer();
