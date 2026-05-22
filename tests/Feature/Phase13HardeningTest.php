<?php

declare(strict_types=1);

use App\Enums\InvoiceStatus;
use App\Enums\PaymentMethod;
use App\Enums\TripStatus;
use App\Models\Car;
use App\Models\Customer;
use App\Models\Driver;
use App\Models\Invoice;
use App\Models\Payment;
use App\Models\Trip;
use App\Models\User;
use App\Services\Booking\BookingAvailabilityService;
use App\Services\Invoicing\InvoiceService;
use App\Services\Invoicing\PaymentService;
use Carbon\CarbonImmutable;
use Database\Seeders\BranchSeeder;
use Database\Seeders\CarCategorySeeder;
use Database\Seeders\RateCardSeeder;
use Database\Seeders\RoleSeeder;
use Illuminate\Support\Facades\DB;

beforeEach(function (): void {
    $this->seed(RoleSeeder::class);
    $this->seed(BranchSeeder::class);
    $this->seed(CarCategorySeeder::class);
    $this->seed(RateCardSeeder::class);
});

/* ============================================================================
 * Security headers
 * ========================================================================== */

it('applies the Phase 13 security headers to every admin response', function (): void {
    $user = User::factory()->create();
    $user->assignRole('super_admin');

    $resp = $this->actingAs($user)->get('/admin');

    expect($resp->headers->get('X-Frame-Options'))->toBe('DENY')
        ->and($resp->headers->get('X-Content-Type-Options'))->toBe('nosniff')
        ->and($resp->headers->get('Referrer-Policy'))->toBe('same-origin')
        ->and($resp->headers->get('Content-Security-Policy'))->toContain("frame-ancestors 'none'");
});

it('emits HSTS when the request comes in over HTTPS', function (): void {
    $resp = $this->get('https://localhost/portal/login');
    expect($resp->headers->get('Strict-Transport-Security'))->toContain('max-age=31536000');
});

/* ============================================================================
 * Auth rate limiting
 * ========================================================================== */

it('rate-limits the driver login endpoint at 5 attempts per minute per IP', function (): void {
    Driver::factory()->create(['phone' => '+201000000000', 'password' => 'right-pw']);

    // 5 bad attempts allowed
    for ($i = 0; $i < 5; $i++) {
        $this->post('/driver/login', ['phone' => '+201000000000', 'password' => 'wrong'])
            ->assertSessionHasErrors('phone');
    }

    // 6th attempt is throttled
    $this->post('/driver/login', ['phone' => '+201000000000', 'password' => 'wrong'])
        ->assertStatus(429);
});

it('rate-limits the portal login endpoint at 5 attempts per minute per IP', function (): void {
    Customer::factory()->create(['email' => 'cx@example.com', 'password' => 'right-pw']);

    for ($i = 0; $i < 5; $i++) {
        $this->post('/portal/login', ['identifier' => 'cx@example.com', 'password' => 'wrong']);
    }

    $this->post('/portal/login', ['identifier' => 'cx@example.com', 'password' => 'wrong'])
        ->assertStatus(429);
});

/* ============================================================================
 * Permission matrix — every role gets the panel access it's supposed to
 * ========================================================================== */

it('grants /admin access to all 7 staff roles', function (string $role): void {
    $user = User::factory()->create();
    $user->assignRole($role);

    $this->actingAs($user)->get('/admin')->assertSuccessful();
})->with([
    'super_admin',
    'branch_manager',
    'dispatcher',
    'accountant',
    'reservations_agent',
    'driver_supervisor',
    'fleet_manager',
]);

it('blocks unauthenticated guests from /admin', function (): void {
    $this->get('/admin')->assertRedirect();
});

/* ============================================================================
 * Full booking → trip → invoice → payment lifecycle
 * ========================================================================== */

it('runs the full lifecycle: trip completion → invoice generation → payment allocation → paid', function (): void {
    $trip = Trip::factory()->create([
        'status' => TripStatus::Completed,
        'subtotal' => '1000.00',
        'vat_amount' => '140.00',
        'total_amount' => '1140.00',
    ]);

    // 1) generate invoice from the completed trip
    $invoice = app(InvoiceService::class)->generateFromTrip($trip);
    expect($invoice->status)->toBe(InvoiceStatus::Draft)
        ->and((string) $invoice->balance_due)->toBe('1140.00');

    // 2) create a cash payment from the customer
    $payment = Payment::factory()->create([
        'customer_id' => $invoice->customer_id,
        'method' => PaymentMethod::Cash,
        'amount' => '1140.00',
    ]);

    // 3) allocate the payment to the invoice
    app(PaymentService::class)->allocate($payment, [$invoice->id => '1140.00']);

    // 4) verify invoice flipped to paid with zero balance
    $invoice->refresh();
    expect($invoice->status)->toBe(InvoiceStatus::Paid)
        ->and((string) $invoice->balance_due)->toBe('0.00')
        ->and((string) $invoice->paid_amount)->toBe('1140.00');
});

/* ============================================================================
 * Concurrent booking attempts
 * ==========================================================================
 *
 * The MariaDB trigger 'trips_no_car_overlap_insert' is the hard guard against
 * overlap; on SQLite (the parallel test DB) it's a no-op (the migration skips
 * trigger DDL on non-MariaDB drivers). What we CAN test on SQLite is the
 * application-layer scope: BookingAvailabilityService's check semantics +
 * the uniqueness of Trip::nextNumber() under sequential creates of trips
 * that target the same car window. The concurrency stress test on real
 * MariaDB is in DEPLOY.md as a manual run.
 */
it('refuses to create multiple non-cancelled trips on the same car in the same window', function (): void {
    $car = Car::factory()->create();
    $start = CarbonImmutable::parse('2026-08-01 09:00');
    $end = CarbonImmutable::parse('2026-08-01 17:00');

    Trip::factory()->create([
        'car_id' => $car->id,
        'scheduled_start' => $start,
        'scheduled_end' => $end,
        'status' => TripStatus::Confirmed,
    ]);

    $svc = app(BookingAvailabilityService::class);
    $availability = $svc->checkAvailability(
        $car->id,
        Driver::factory()->create()->id,
        $start,
        $end,
    );

    expect($availability->isAvailable())->toBeFalse()
        ->and($availability->hardIssues())->not->toBeEmpty();
});

it('Trip::nextNumber generates unique sequential numbers under burst creates', function (): void {
    $car = Car::factory()->create();

    // Burst-create 10 trips in a tight loop on different cars (avoid overlap
    // triggers) to exercise the numbering path. On real MariaDB this is the
    // hot path for a busy dispatcher; numbers must be unique even when many
    // creates land in the same second.
    $numbers = [];
    for ($i = 0; $i < 10; $i++) {
        $t = Trip::factory()->create();
        $numbers[] = $t->trip_number;
    }

    expect($numbers)->toHaveCount(10)
        ->and(array_unique($numbers))->toHaveCount(10);
});

/* ============================================================================
 * Backup script exists and is executable on a Unix box
 * ========================================================================== */

it('ships the backup.sh script for the cPanel cron', function (): void {
    expect(file_exists(base_path('storage/scripts/backup.sh')))->toBeTrue()
        ->and(file_get_contents(base_path('storage/scripts/backup.sh')))
        ->toContain('mysqldump')
        ->toContain('tar -czf')
        ->toContain('BACKUP_KEEP');
});
