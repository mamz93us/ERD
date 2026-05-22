<?php

declare(strict_types=1);

use App\Http\Controllers\Driver\AuthController as DriverAuthController;
use App\Http\Controllers\Driver\DashboardController;
use App\Http\Controllers\Driver\ExpenseController;
use App\Http\Controllers\Driver\PayrollController;
use App\Http\Controllers\Driver\TripController;
use App\Http\Controllers\Portal\AuthController as PortalAuthController;
use App\Http\Controllers\Portal\PortalController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

/*
 * Phase 10 — Driver mobile-friendly web app at /driver.
 */
Route::prefix('driver')->name('driver.')->group(function () {
    Route::middleware('guest:driver')->group(function () {
        Route::get('login', [DriverAuthController::class, 'showLogin'])->name('login');
        Route::post('login', [DriverAuthController::class, 'login'])
            ->middleware('throttle:5,1')      // Phase 13: 5 attempts / minute / IP
            ->name('login.submit');
    });

    Route::middleware('auth:driver')->group(function () {
        Route::get('/', [DashboardController::class, 'index'])->name('dashboard');
        Route::post('logout', [DriverAuthController::class, 'logout'])->name('logout');

        Route::get('trips/{trip}', [TripController::class, 'show'])->name('trips.show');
        Route::post('trips/{trip}/start', [TripController::class, 'start'])->name('trips.start');
        Route::post('trips/{trip}/end', [TripController::class, 'end'])->name('trips.end');

        Route::get('trips/{trip}/expenses/create', [ExpenseController::class, 'create'])->name('expenses.create');
        Route::post('trips/{trip}/expenses', [ExpenseController::class, 'store'])->name('expenses.store');

        Route::get('payroll', [PayrollController::class, 'index'])->name('payroll');
    });
});

/*
 * Phase 11 — Customer portal at /portal. Customer logs in with email or
 * phone + password. 7 page types per spec §6 Phase 11.
 */
Route::prefix('portal')->name('portal.')->group(function () {
    Route::middleware('guest:customer')->group(function () {
        Route::get('login', [PortalAuthController::class, 'showLogin'])->name('login');
        Route::post('login', [PortalAuthController::class, 'login'])
            ->middleware('throttle:5,1')      // Phase 13: 5 attempts / minute / IP
            ->name('login.submit');
    });

    Route::middleware('auth:customer')->group(function () {
        Route::get('/', [PortalController::class, 'dashboard'])->name('dashboard');
        Route::post('logout', [PortalAuthController::class, 'logout'])->name('logout');

        Route::get('trips', [PortalController::class, 'trips'])->name('trips.index');
        Route::get('trips/{trip}', [PortalController::class, 'tripShow'])->name('trips.show');

        Route::get('quotations', [PortalController::class, 'quotations'])->name('quotations.index');
        Route::get('quotations/{quotation}', [PortalController::class, 'quotationShow'])->name('quotations.show');
        Route::post('quotations/{quotation}/decide', [PortalController::class, 'quotationDecide'])->name('quotations.decide');

        Route::get('invoices', [PortalController::class, 'invoices'])->name('invoices.index');
        Route::get('invoices/{invoice}/pdf', [PortalController::class, 'invoicePdf'])->name('invoices.pdf');

        Route::get('booking/new', [PortalController::class, 'bookingRequestForm'])->name('booking.create');
        Route::post('booking', [PortalController::class, 'bookingRequestStore'])->name('booking.store');

        Route::get('profile', [PortalController::class, 'profile'])->name('profile');
    });
});
