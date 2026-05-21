<?php

declare(strict_types=1);

use App\Enums\TripStatus;
use App\Models\Car;
use App\Models\Driver;
use App\Models\TrafficFine;
use App\Models\Trip;
use App\Models\User;
use App\Services\Compliance\TrafficFineAttributionService;
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

it('TrafficFineAttributionService finds the trip active for this car at the violation timestamp', function (): void {
    $car = Car::factory()->create();
    $driver = Driver::factory()->create();
    $trip = Trip::factory()->create([
        'car_id' => $car->id,
        'driver_id' => $driver->id,
        'scheduled_start' => CarbonImmutable::parse('2026-06-01 09:00'),
        'scheduled_end' => CarbonImmutable::parse('2026-06-01 17:00'),
        'status' => TripStatus::InProgress,
    ]);

    $fine = new TrafficFine([
        'car_id' => $car->id,
        'violation_date' => '2026-06-01 12:30',
        'violation_number' => 'V-TEST-1',
        'violation_type' => 'speeding',
        'amount' => 500,
    ]);

    app(TrafficFineAttributionService::class)->attribute($fine);

    expect($fine->trip_id)->toBe($trip->id)
        ->and($fine->driver_id)->toBe($driver->id);
});

it('TrafficFineAttributionService is a no-op when no trip was active for this car at the violation time', function (): void {
    $car = Car::factory()->create();
    $fine = new TrafficFine([
        'car_id' => $car->id,
        'violation_date' => '2026-06-01 12:00',
        'violation_number' => 'V-TEST-2',
        'violation_type' => 'parking',
        'amount' => 200,
    ]);

    app(TrafficFineAttributionService::class)->attribute($fine);

    expect($fine->trip_id)->toBeNull();
});

it('TrafficFineAttributionService is a no-op when trip_id is already set (manual override wins)', function (): void {
    $car = Car::factory()->create();
    $driver = Driver::factory()->create();
    Trip::factory()->create([
        'car_id' => $car->id,
        'driver_id' => $driver->id,
        'scheduled_start' => CarbonImmutable::parse('2026-06-01 09:00'),
        'scheduled_end' => CarbonImmutable::parse('2026-06-01 17:00'),
        'status' => TripStatus::InProgress,
    ]);

    $existingTripId = '019e0000-0000-0000-0000-000000000001';
    $fine = new TrafficFine([
        'car_id' => $car->id,
        'trip_id' => $existingTripId,
        'violation_date' => '2026-06-01 12:00',
        'violation_number' => 'V-TEST-3',
        'violation_type' => 'speeding',
        'amount' => 300,
    ]);

    app(TrafficFineAttributionService::class)->attribute($fine);

    expect($fine->trip_id)->toBe($existingTripId);  // unchanged
});

it('TrafficFineAttributionService ignores cancelled trips when looking for an active match', function (): void {
    $car = Car::factory()->create();
    Trip::factory()->create([
        'car_id' => $car->id,
        'scheduled_start' => CarbonImmutable::parse('2026-06-01 09:00'),
        'scheduled_end' => CarbonImmutable::parse('2026-06-01 17:00'),
        'status' => TripStatus::Cancelled,
    ]);

    $fine = new TrafficFine([
        'car_id' => $car->id,
        'violation_date' => '2026-06-01 12:00',
        'violation_number' => 'V-TEST-4',
        'violation_type' => 'speeding',
        'amount' => 400,
    ]);

    app(TrafficFineAttributionService::class)->attribute($fine);

    expect($fine->trip_id)->toBeNull();
});

it('TrafficFineObserver attributes on create automatically', function (): void {
    $car = Car::factory()->create();
    $driver = Driver::factory()->create();
    $trip = Trip::factory()->create([
        'car_id' => $car->id,
        'driver_id' => $driver->id,
        'scheduled_start' => CarbonImmutable::parse('2026-07-15 09:00'),
        'scheduled_end' => CarbonImmutable::parse('2026-07-15 17:00'),
        'status' => TripStatus::InProgress,
    ]);

    $fine = TrafficFine::factory()->create([
        'car_id' => $car->id,
        'violation_date' => '2026-07-15 11:00',
    ]);

    expect($fine->trip_id)->toBe($trip->id)
        ->and($fine->driver_id)->toBe($driver->id);
});

it('Deduct-from-payroll updates deducted_from_driver flag', function (): void {
    $fine = TrafficFine::factory()->create(['deducted_from_driver' => false]);
    $fine->update(['deducted_from_driver' => true]);

    expect($fine->fresh()->deducted_from_driver)->toBeTrue();
});

it('renders the compliance + schedule admin pages for super_admin', function (string $path): void {
    $user = User::factory()->create();
    $user->assignRole('super_admin');

    $this->actingAs($user)->get($path)->assertSuccessful();
})->with(['/admin/traffic-fines', '/admin/insurance-claims', '/admin/trip-schedule']);
