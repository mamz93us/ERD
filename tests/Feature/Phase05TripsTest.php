<?php

declare(strict_types=1);

use App\Enums\CarDocumentType;
use App\Enums\TripStatus;
use App\Models\Branch;
use App\Models\Car;
use App\Models\CarDocument;
use App\Models\Driver;
use App\Models\DriverDocument;
use App\Models\Trip;
use App\Models\User;
use App\Services\Booking\BookingAvailabilityService;
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
    $this->service = app(BookingAvailabilityService::class);
});

it('auto-generates trip_number in T-YYYY-NNNN format and increments', function (): void {
    $a = Trip::factory()->create();
    $b = Trip::factory()->create();
    $year = now()->year;

    expect($a->trip_number)->toBe("T-{$year}-0001")
        ->and($b->trip_number)->toBe("T-{$year}-0002");
});

it('returns no issues for a clean booking window', function (): void {
    $car = Car::factory()->create();
    $driver = Driver::factory()->create();

    $result = $this->service->checkAvailability(
        carId: $car->id,
        driverId: $driver->id,
        scheduledStart: CarbonImmutable::parse('+2 days 09:00'),
        scheduledEnd: CarbonImmutable::parse('+2 days 17:00'),
    );

    expect($result->isAvailable())->toBeTrue()
        ->and($result->issues)->toBe([]);
});

it('detects car overlap with another active trip (±2hr buffer)', function (): void {
    $branch = Branch::query()->first();
    $car = Car::factory()->create(['branch_id' => $branch->id]);
    $driver1 = Driver::factory()->create(['branch_id' => $branch->id]);
    $driver2 = Driver::factory()->create(['branch_id' => $branch->id]);

    // Existing trip 09:00 → 17:00
    Trip::factory()->create([
        'branch_id' => $branch->id,
        'car_id' => $car->id,
        'driver_id' => $driver1->id,
        'scheduled_start' => CarbonImmutable::parse('+2 days 09:00'),
        'scheduled_end' => CarbonImmutable::parse('+2 days 17:00'),
        'status' => TripStatus::Confirmed,
    ]);

    // New trip 18:00 → 20:00 — only 1h gap, within the 2h buffer → conflict
    $result = $this->service->checkAvailability(
        carId: $car->id,
        driverId: $driver2->id,
        scheduledStart: CarbonImmutable::parse('+2 days 18:00'),
        scheduledEnd: CarbonImmutable::parse('+2 days 20:00'),
    );

    expect($result->isAvailable())->toBeFalse()
        ->and($result->hardIssues())->toHaveCount(1)
        ->and($result->hardIssues()[0]->type)->toBe('car_conflict');
});

it('does NOT flag cancelled trips as conflicts', function (): void {
    $branch = Branch::query()->first();
    $car = Car::factory()->create(['branch_id' => $branch->id]);
    $driver = Driver::factory()->create(['branch_id' => $branch->id]);

    Trip::factory()->create([
        'branch_id' => $branch->id,
        'car_id' => $car->id,
        'driver_id' => $driver->id,
        'scheduled_start' => CarbonImmutable::parse('+2 days 09:00'),
        'scheduled_end' => CarbonImmutable::parse('+2 days 17:00'),
        'status' => TripStatus::Cancelled,
    ]);

    $result = $this->service->checkAvailability(
        carId: $car->id,
        driverId: $driver->id,
        scheduledStart: CarbonImmutable::parse('+2 days 09:00'),
        scheduledEnd: CarbonImmutable::parse('+2 days 17:00'),
    );

    expect($result->isAvailable())->toBeTrue();
});

it('excludes the trip being edited from its own conflict check', function (): void {
    $branch = Branch::query()->first();
    $car = Car::factory()->create(['branch_id' => $branch->id]);
    $driver = Driver::factory()->create(['branch_id' => $branch->id]);

    $trip = Trip::factory()->create([
        'branch_id' => $branch->id,
        'car_id' => $car->id,
        'driver_id' => $driver->id,
        'scheduled_start' => CarbonImmutable::parse('+2 days 09:00'),
        'scheduled_end' => CarbonImmutable::parse('+2 days 17:00'),
        'status' => TripStatus::Confirmed,
    ]);

    $result = $this->service->checkAvailability(
        carId: $car->id,
        driverId: $driver->id,
        scheduledStart: CarbonImmutable::parse('+2 days 09:00'),
        scheduledEnd: CarbonImmutable::parse('+2 days 17:00'),
        excludeTripId: $trip->id,
    );

    expect($result->isAvailable())->toBeTrue();
});

it('detects driver overlap separately from car overlap', function (): void {
    $branch = Branch::query()->first();
    $car1 = Car::factory()->create(['branch_id' => $branch->id]);
    $car2 = Car::factory()->create(['branch_id' => $branch->id]);
    $driver = Driver::factory()->create(['branch_id' => $branch->id]);

    Trip::factory()->create([
        'branch_id' => $branch->id,
        'car_id' => $car1->id,
        'driver_id' => $driver->id,
        'scheduled_start' => CarbonImmutable::parse('+2 days 09:00'),
        'scheduled_end' => CarbonImmutable::parse('+2 days 17:00'),
        'status' => TripStatus::Confirmed,
    ]);

    // Different car, same driver, overlapping window
    $result = $this->service->checkAvailability(
        carId: $car2->id,
        driverId: $driver->id,
        scheduledStart: CarbonImmutable::parse('+2 days 12:00'),
        scheduledEnd: CarbonImmutable::parse('+2 days 18:00'),
    );

    $types = array_column(array_map(fn ($i) => $i->toArray(), $result->hardIssues()), 'type');

    expect($result->isAvailable())->toBeFalse()
        ->and($types)->toContain('driver_conflict')
        ->and($types)->not->toContain('car_conflict');
});

it('flags car document expiring inside the window as SOFT issue', function (): void {
    $car = Car::factory()->create();
    $driver = Driver::factory()->create();
    CarDocument::factory()->create([
        'car_id' => $car->id,
        'doc_type' => CarDocumentType::RegistrationLicense,
        'is_active' => true,
        'expiry_date' => now()->addDays(5),
    ]);

    $result = $this->service->checkAvailability(
        carId: $car->id,
        driverId: $driver->id,
        scheduledStart: CarbonImmutable::parse('+1 day 09:00'),
        scheduledEnd: CarbonImmutable::parse('+10 days 17:00'),
    );

    expect($result->isAvailable())->toBeTrue() // soft → still available
        ->and($result->softIssues())->toHaveCount(1)
        ->and($result->softIssues()[0]->type)->toBe('car_document_expiry');
});

it('flags driver document expiring inside the window as SOFT issue', function (): void {
    $car = Car::factory()->create();
    $driver = Driver::factory()->create();
    DriverDocument::factory()->create([
        'driver_id' => $driver->id,
        'is_active' => true,
        'expiry_date' => now()->addDays(5),
    ]);

    $result = $this->service->checkAvailability(
        carId: $car->id,
        driverId: $driver->id,
        scheduledStart: CarbonImmutable::parse('+1 day 09:00'),
        scheduledEnd: CarbonImmutable::parse('+10 days 17:00'),
    );

    $softTypes = array_column(array_map(fn ($i) => $i->toArray(), $result->softIssues()), 'type');

    expect($softTypes)->toContain('driver_document_expiry');
});

it('hard-blocks a sub_rented car booking with no active contract', function (): void {
    $car = Car::factory()->subRented()->create();
    $driver = Driver::factory()->create();

    $result = $this->service->checkAvailability(
        carId: $car->id,
        driverId: $driver->id,
        scheduledStart: CarbonImmutable::parse('+2 days 09:00'),
        scheduledEnd: CarbonImmutable::parse('+2 days 17:00'),
    );

    $types = array_column(array_map(fn ($i) => $i->toArray(), $result->hardIssues()), 'type');

    expect($result->isAvailable())->toBeFalse()
        ->and($types)->toContain('sub_rental_coverage');
});

it('Trip::changeStatus enforces the transition rules', function (): void {
    $trip = Trip::factory()->create(['status' => TripStatus::Draft]);

    // Draft → Confirmed is allowed
    $trip->changeStatus(TripStatus::Confirmed);
    expect($trip->fresh()->status)->toBe(TripStatus::Confirmed);

    // Confirmed → InProgress is NOT allowed (must go through Assigned then EnRoute)
    expect(fn () => $trip->changeStatus(TripStatus::InProgress))
        ->toThrow(RuntimeException::class);
});

it('Trip::changeStatus stores cancellation_reason when cancelling', function (): void {
    $trip = Trip::factory()->create(['status' => TripStatus::Draft]);

    $trip->changeStatus(TripStatus::Cancelled, 'customer changed plans');

    expect($trip->fresh()->status)->toBe(TripStatus::Cancelled)
        ->and($trip->fresh()->cancellation_reason)->toBe('customer changed plans');
});

it('hides other branches trips via BelongsToBranch scope', function (): void {
    $abh = Branch::query()->where('code', 'ABH')->firstOrFail();
    $cai = Branch::query()->where('code', 'CAI')->firstOrFail();

    Trip::factory()->create(['branch_id' => $abh->id]);
    Trip::factory()->count(2)->create(['branch_id' => $cai->id]);

    $abhManager = User::factory()->create(['branch_id' => $abh->id]);
    $abhManager->assignRole('branch_manager');
    $this->actingAs($abhManager);

    expect(Trip::query()->count())->toBe(1);
});

it('renders the trips admin index page for super_admin', function (): void {
    $user = User::factory()->create();
    $user->assignRole('super_admin');

    $this->actingAs($user)->get('/admin/trips')->assertSuccessful();
});
