<?php

declare(strict_types=1);

use App\Http\Controllers\Driver\AuthController;
use App\Http\Controllers\Driver\DashboardController;
use App\Http\Controllers\Driver\ExpenseController;
use App\Http\Controllers\Driver\PayrollController;
use App\Http\Controllers\Driver\TripController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

/*
 * Phase 10 — Driver mobile-friendly web app at /driver. Separate session
 * guard from the admin (Filament). No Inertia/Vue — plain blade so it
 * works on cheap phones with weak browsers.
 */
Route::prefix('driver')->name('driver.')->group(function () {
    Route::middleware('guest:driver')->group(function () {
        Route::get('login', [AuthController::class, 'showLogin'])->name('login');
        Route::post('login', [AuthController::class, 'login'])->name('login.submit');
    });

    Route::middleware('auth:driver')->group(function () {
        Route::get('/', [DashboardController::class, 'index'])->name('dashboard');
        Route::post('logout', [AuthController::class, 'logout'])->name('logout');

        Route::get('trips/{trip}', [TripController::class, 'show'])->name('trips.show');
        Route::post('trips/{trip}/start', [TripController::class, 'start'])->name('trips.start');
        Route::post('trips/{trip}/end', [TripController::class, 'end'])->name('trips.end');

        Route::get('trips/{trip}/expenses/create', [ExpenseController::class, 'create'])->name('expenses.create');
        Route::post('trips/{trip}/expenses', [ExpenseController::class, 'store'])->name('expenses.store');

        Route::get('payroll', [PayrollController::class, 'index'])->name('payroll');
    });
});
