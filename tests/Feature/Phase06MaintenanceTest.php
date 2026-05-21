<?php

declare(strict_types=1);

use App\Enums\CarStatus;
use App\Enums\MaintenanceOrderStatus;
use App\Enums\MaintenanceServiceType;
use App\Models\Car;
use App\Models\Driver;
use App\Models\Garage;
use App\Models\MaintenanceItem;
use App\Models\MaintenanceOrder;
use App\Models\MaintenanceSchedule;
use App\Models\User;
use App\Services\Booking\BookingAvailabilityService;
use Carbon\CarbonImmutable;
use Database\Seeders\BranchSeeder;
use Database\Seeders\CarCategorySeeder;
use Database\Seeders\GarageSeeder;
use Database\Seeders\RateCardSeeder;
use Database\Seeders\RoleSeeder;

beforeEach(function (): void {
    $this->seed(RoleSeeder::class);
    $this->seed(BranchSeeder::class);
    $this->seed(CarCategorySeeder::class);
    $this->seed(GarageSeeder::class);
    $this->seed(RateCardSeeder::class);
});

it('auto-generates maintenance order_number in M-YYYY-NNNN format and increments', function (): void {
    $a = MaintenanceOrder::factory()->create();
    $b = MaintenanceOrder::factory()->create();
    $year = now()->year;

    expect($a->order_number)->toBe("M-{$year}-0001")
        ->and($b->order_number)->toBe("M-{$year}-0002");
});

it('MaintenanceItem auto-computes total_cost as quantity × unit_cost on save', function (): void {
    $order = MaintenanceOrder::factory()->create();
    $item = MaintenanceItem::factory()->create([
        'maintenance_order_id' => $order->id,
        'quantity' => 2.5,
        'unit_cost' => 100,
    ]);

    expect((float) $item->total_cost)->toBe(250.0);
});

it('MaintenanceSchedule observer computes next_due_km/date from intervals on create', function (): void {
    $car = Car::factory()->create(['current_odometer' => 50_000]);
    $schedule = MaintenanceSchedule::query()->create([
        'car_id' => $car->id,
        'service_type' => MaintenanceServiceType::OilChange,
        'interval_km' => 5000,
        'interval_days' => 90,
        'is_active' => true,
    ]);
    $fresh = $schedule->fresh();

    expect($fresh->next_due_km)->toBe(55_000)
        ->and($fresh->next_due_date)->not->toBeNull()
        ->and($fresh->next_due_date->isAfter(now()->addDays(89)))->toBeTrue();
});

it('flips car status to in_maintenance when order moves to in_service', function (): void {
    $car = Car::factory()->create(['status' => CarStatus::Available]);
    $order = MaintenanceOrder::factory()->create(['car_id' => $car->id, 'status' => MaintenanceOrderStatus::Scheduled]);

    $order->update(['status' => MaintenanceOrderStatus::InService]);

    expect($car->fresh()->status)->toBe(CarStatus::InMaintenance);
});

it('flips car status back to available when order completes', function (): void {
    $car = Car::factory()->create(['status' => CarStatus::Available]);
    $order = MaintenanceOrder::factory()->create(['car_id' => $car->id, 'status' => MaintenanceOrderStatus::Scheduled]);

    $order->update(['status' => MaintenanceOrderStatus::InService]);
    expect($car->fresh()->status)->toBe(CarStatus::InMaintenance);

    $order->update(['status' => MaintenanceOrderStatus::Completed, 'actual_end' => now(), 'odometer_at_service' => 60_000]);

    expect($car->fresh()->status)->toBe(CarStatus::Available);
});

it('recomputes next_due_km/date on schedule after completed order', function (): void {
    $car = Car::factory()->create(['current_odometer' => 50_000]);
    $schedule = MaintenanceSchedule::query()->create([
        'car_id' => $car->id,
        'service_type' => MaintenanceServiceType::OilChange,
        'interval_km' => 5000,
        'interval_days' => 90,
        'is_active' => true,
    ]);

    $order = MaintenanceOrder::factory()->create([
        'car_id' => $car->id,
        'status' => MaintenanceOrderStatus::Scheduled,
    ]);

    $order->update([
        'status' => MaintenanceOrderStatus::Completed,
        'actual_end' => now(),
        'odometer_at_service' => 62_000,
    ]);

    $fresh = $schedule->fresh();
    expect((int) $fresh->last_done_km)->toBe(62_000)
        ->and((int) $fresh->next_due_km)->toBe(67_000);
});

it('CheckMaintenanceDue creates a draft order when a schedule is overdue by date', function (): void {
    $car = Car::factory()->create(['current_odometer' => 10_000]);
    MaintenanceSchedule::withoutEvents(fn () => MaintenanceSchedule::query()->create([
        'car_id' => $car->id,
        'service_type' => MaintenanceServiceType::OilChange,
        'interval_km' => null,
        'interval_days' => 30,
        'next_due_date' => now()->subDays(1),
        'is_active' => true,
    ]));

    expect(MaintenanceOrder::query()->where('car_id', $car->id)->count())->toBe(0);

    $this->artisan('maintenance:check-due')->assertSuccessful();

    expect(MaintenanceOrder::query()->where('car_id', $car->id)->count())->toBe(1);
});

it('CheckMaintenanceDue creates a draft when km-due', function (): void {
    $car = Car::factory()->create(['current_odometer' => 60_000]);
    MaintenanceSchedule::withoutEvents(fn () => MaintenanceSchedule::query()->create([
        'car_id' => $car->id,
        'service_type' => MaintenanceServiceType::TireRotation,
        'interval_km' => 10_000,
        'interval_days' => null,
        'next_due_km' => 55_000,
        'is_active' => true,
    ]));

    $this->artisan('maintenance:check-due')->assertSuccessful();

    expect(MaintenanceOrder::query()->where('car_id', $car->id)->count())->toBe(1);
});

it('CheckMaintenanceDue does NOT create duplicate when a pending order already exists', function (): void {
    $car = Car::factory()->create(['current_odometer' => 60_000]);
    $garage = Garage::query()->first();
    MaintenanceOrder::factory()->create([
        'car_id' => $car->id,
        'garage_id' => $garage->id,
        'status' => MaintenanceOrderStatus::Scheduled,
    ]);
    MaintenanceSchedule::withoutEvents(fn () => MaintenanceSchedule::query()->create([
        'car_id' => $car->id,
        'service_type' => MaintenanceServiceType::OilChange,
        'interval_days' => 30,
        'next_due_date' => now()->subDays(1),
        'is_active' => true,
    ]));

    $countBefore = MaintenanceOrder::query()->where('car_id', $car->id)->count();
    $this->artisan('maintenance:check-due')->assertSuccessful();
    $countAfter = MaintenanceOrder::query()->where('car_id', $car->id)->count();

    expect($countAfter)->toBe($countBefore);
});

it('BookingAvailabilityService now reports maintenance_conflict (Phase 6 lights up Phase 5 placeholder)', function (): void {
    $car = Car::factory()->create();
    $driver = Driver::factory()->create();
    $garage = Garage::query()->first();

    MaintenanceOrder::factory()->create([
        'car_id' => $car->id,
        'garage_id' => $garage->id,
        'scheduled_start' => CarbonImmutable::parse('+2 days 09:00'),
        'scheduled_end' => CarbonImmutable::parse('+2 days 17:00'),
        'status' => MaintenanceOrderStatus::Scheduled,
    ]);

    $result = app(BookingAvailabilityService::class)->checkAvailability(
        carId: $car->id,
        driverId: $driver->id,
        scheduledStart: CarbonImmutable::parse('+2 days 12:00'),
        scheduledEnd: CarbonImmutable::parse('+2 days 14:00'),
    );

    $types = array_column(array_map(fn ($i) => $i->toArray(), $result->hardIssues()), 'type');

    expect($result->isAvailable())->toBeFalse()
        ->and($types)->toContain('maintenance_conflict');
});

it('renders the maintenance admin pages for super_admin', function (string $path): void {
    $user = User::factory()->create();
    $user->assignRole('super_admin');

    $this->actingAs($user)->get($path)->assertSuccessful();
})->with(['/admin/maintenance-schedules', '/admin/maintenance-orders']);
