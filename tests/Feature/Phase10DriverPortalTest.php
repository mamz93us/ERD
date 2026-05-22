<?php

declare(strict_types=1);

use App\Enums\TripExpenseType;
use App\Enums\TripStatus;
use App\Models\Driver;
use App\Models\TrafficFine;
use App\Models\Trip;
use App\Models\TripExpense;
use Carbon\CarbonImmutable;
use Database\Seeders\BranchSeeder;
use Database\Seeders\CarCategorySeeder;
use Database\Seeders\RateCardSeeder;
use Database\Seeders\RoleSeeder;

beforeEach(function (): void {
    $this->seed(RoleSeeder::class);
    $this->seed(BranchSeeder::class);
    $this->seed(CarCategorySeeder::class);
    $this->seed(RateCardSeeder::class);
});

/* ============================================================================
 * Auth
 * ========================================================================== */

it('shows the driver login page at /driver/login', function (): void {
    $this->get('/driver/login')
        ->assertOk()
        ->assertSee('name="phone"', false)
        ->assertSee('name="password"', false);
});

it('lets a driver log in with phone + password and redirects to /driver', function (): void {
    $driver = Driver::factory()->create([
        'phone' => '+201001112233',
        'password' => 'secret-pw',
    ]);

    $this->post('/driver/login', [
        'phone' => '+201001112233',
        'password' => 'secret-pw',
    ])->assertRedirect('/driver');

    $this->assertAuthenticatedAs($driver, 'driver');
});

it('rejects an invalid password with a validation error on phone', function (): void {
    Driver::factory()->create(['phone' => '+201001112233', 'password' => 'right-pw']);

    $this->post('/driver/login', ['phone' => '+201001112233', 'password' => 'wrong-pw'])
        ->assertSessionHasErrors('phone');

    $this->assertGuest('driver');
});

it('blocks /driver dashboard for guests and redirects to login', function (): void {
    $this->get('/driver')->assertRedirect('/driver/login');
});

it('logs the driver out and clears the session', function (): void {
    $driver = Driver::factory()->create();
    $this->actingAs($driver, 'driver')
        ->post('/driver/logout')
        ->assertRedirect('/driver/login');

    $this->assertGuest('driver');
});

/* ============================================================================
 * Dashboard
 * ========================================================================== */

it("shows today's trips on the dashboard for the logged-in driver", function (): void {
    $driver = Driver::factory()->create();
    $myTrip = Trip::factory()->create([
        'driver_id' => $driver->id,
        'scheduled_start' => CarbonImmutable::today()->setHour(9),
        'scheduled_end' => CarbonImmutable::today()->setHour(17),
        'status' => TripStatus::Confirmed,
    ]);
    $otherDriversTrip = Trip::factory()->create([
        'scheduled_start' => CarbonImmutable::today()->setHour(10),
        'scheduled_end' => CarbonImmutable::today()->setHour(15),
    ]);

    $this->actingAs($driver, 'driver')
        ->get('/driver')
        ->assertOk()
        ->assertSee($myTrip->trip_number)
        ->assertDontSee($otherDriversTrip->trip_number);
});

/* ============================================================================
 * Trip start/end
 * ========================================================================== */

it('starts an assigned trip and flips status to in_progress', function (): void {
    $driver = Driver::factory()->create();
    $trip = Trip::factory()->create([
        'driver_id' => $driver->id,
        'status' => TripStatus::Assigned,
    ]);

    $this->actingAs($driver, 'driver')
        ->post("/driver/trips/{$trip->id}/start", ['start_odometer' => 12000])
        ->assertRedirect();

    $trip->refresh();
    expect($trip->status)->toBe(TripStatus::InProgress)
        ->and($trip->start_odometer)->toBe(12000)
        ->and($trip->actual_start)->not->toBeNull();
});

it('refuses to start a trip in a non-startable status (e.g. completed)', function (): void {
    $driver = Driver::factory()->create();
    $trip = Trip::factory()->create([
        'driver_id' => $driver->id,
        'status' => TripStatus::Completed,
    ]);

    $this->actingAs($driver, 'driver')
        ->post("/driver/trips/{$trip->id}/start", ['start_odometer' => 9999])
        ->assertSessionHasErrors('status');

    expect($trip->fresh()->status)->toBe(TripStatus::Completed);
});

it("won't let a driver start another driver's trip (404)", function (): void {
    $driver = Driver::factory()->create();
    $otherTrip = Trip::factory()->create(['status' => TripStatus::Assigned]);

    $this->actingAs($driver, 'driver')
        ->post("/driver/trips/{$otherTrip->id}/start", ['start_odometer' => 1])
        ->assertNotFound();
});

it('ends an in-progress trip and flips status to completed', function (): void {
    $driver = Driver::factory()->create();
    $trip = Trip::factory()->create([
        'driver_id' => $driver->id,
        'status' => TripStatus::InProgress,
        'start_odometer' => 10000,
    ]);

    $this->actingAs($driver, 'driver')
        ->post("/driver/trips/{$trip->id}/end", ['end_odometer' => 10350])
        ->assertRedirect();

    $trip->refresh();
    expect($trip->status)->toBe(TripStatus::Completed)
        ->and($trip->end_odometer)->toBe(10350)
        ->and($trip->actual_end)->not->toBeNull();
});

/* ============================================================================
 * Expenses
 * ========================================================================== */

it('records a trip expense submitted by the driver', function (): void {
    $driver = Driver::factory()->create();
    $trip = Trip::factory()->create(['driver_id' => $driver->id]);

    $this->actingAs($driver, 'driver')
        ->post("/driver/trips/{$trip->id}/expenses", [
            'type' => TripExpenseType::Fuel->value,
            'amount' => '150.00',
            'notes' => 'fill-up before pickup',
        ])
        ->assertRedirect();

    $expense = TripExpense::query()->where('trip_id', $trip->id)->first();
    expect($expense)->not->toBeNull()
        ->and((string) $expense->amount)->toBe('150.00')
        ->and($expense->type->value)->toBe(TripExpenseType::Fuel->value);
});

/* ============================================================================
 * Payroll
 * ========================================================================== */

it('shows commission minus deducted fines on the payroll page', function (): void {
    $driver = Driver::factory()->create(['trip_commission_percentage' => '10.00']);

    // Two completed trips at 1000 EGP each = 100 commission each = 200 gross
    for ($i = 0; $i < 2; $i++) {
        Trip::factory()->create([
            'driver_id' => $driver->id,
            'status' => TripStatus::Completed,
            'subtotal' => '1000.00',
            'scheduled_end' => CarbonImmutable::today(),
        ]);
    }

    // A 70 EGP fine deducted from driver
    TrafficFine::factory()->create([
        'driver_id' => $driver->id,
        'deducted_from_driver' => true,
        'amount' => '70.00',
        'violation_date' => CarbonImmutable::today(),
    ]);

    $resp = $this->actingAs($driver, 'driver')->get('/driver/payroll');

    $resp->assertOk()
        ->assertSee('200.00') // gross commission
        ->assertSee('70.00')  // fine
        ->assertSee('130.00'); // net
});
