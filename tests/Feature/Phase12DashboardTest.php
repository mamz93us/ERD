<?php

declare(strict_types=1);

use App\Enums\CarStatus;
use App\Enums\TripStatus;
use App\Filament\Admin\Widgets\DriverLeaderboardWidget;
use App\Filament\Admin\Widgets\OperationsStatsWidget;
use App\Filament\Admin\Widgets\OutstandingReceivablesWidget;
use App\Filament\Admin\Widgets\RevenueComparisonWidget;
use App\Models\Car;
use App\Models\Customer;
use App\Models\Driver;
use App\Models\Invoice;
use App\Models\Trip;
use App\Models\User;
use Carbon\CarbonImmutable;
use Database\Seeders\BranchSeeder;
use Database\Seeders\CarCategorySeeder;
use Database\Seeders\RateCardSeeder;
use Database\Seeders\RoleSeeder;
use Database\Seeders\TranslationSeeder;
use Livewire\Livewire;

beforeEach(function (): void {
    $this->seed(RoleSeeder::class);
    $this->seed(BranchSeeder::class);
    $this->seed(CarCategorySeeder::class);
    $this->seed(RateCardSeeder::class);
    $this->seed(TranslationSeeder::class);
});

it('renders the admin dashboard with all Phase 12 widgets', function (): void {
    $user = User::factory()->create();
    $user->assignRole('super_admin');

    $this->actingAs($user)
        ->get('/admin')
        ->assertSuccessful();
});

it('OperationsStatsWidget renders without error and computes counts', function (): void {
    $user = User::factory()->create();
    $user->assignRole('super_admin');

    Car::factory()->count(3)->create(['status' => CarStatus::Available]);
    Car::factory()->count(2)->create(['status' => CarStatus::InMaintenance]);
    Trip::factory()->create(['status' => TripStatus::InProgress]);
    Trip::factory()->create(['status' => TripStatus::Confirmed]);

    $this->actingAs($user);

    $widget = new OperationsStatsWidget;
    $reflection = new ReflectionMethod($widget, 'getStats');
    $reflection->setAccessible(true);
    $stats = $reflection->invoke($widget);

    expect($stats)->toHaveCount(4);
    // Stat #1 = active trips count (should be 2 from seed)
    $values = array_map(fn ($s) => $s->getValue(), $stats);
    expect($values[0])->toBe('2')        // 2 active trips
        ->and($values[3])->toBe('2');    // 2 cars in maintenance
});

it('RevenueComparisonWidget computes this month vs last month', function (): void {
    $user = User::factory()->create();
    $user->assignRole('super_admin');

    Invoice::factory()->create([
        'issue_date' => CarbonImmutable::now()->startOfMonth()->addDay()->toDateString(),
        'total' => '1500.00',
        'status' => 'sent',
    ]);
    Invoice::factory()->create([
        'issue_date' => CarbonImmutable::now()->subMonth()->startOfMonth()->addDay()->toDateString(),
        'total' => '1000.00',
        'status' => 'sent',
    ]);

    $this->actingAs($user);

    Livewire::test(RevenueComparisonWidget::class)
        ->assertSee('1,500.00')
        ->assertSee('1,000.00');
});

it('OutstandingReceivablesWidget lists customers with unpaid balances, top 10 by amount', function (): void {
    $user = User::factory()->create();
    $user->assignRole('super_admin');

    $bigOwner = Customer::factory()->create(['full_name' => 'Big Spender']);
    Invoice::factory()->create([
        'customer_id' => $bigOwner->id,
        'balance_due' => '9999.99',
        'status' => 'sent',
    ]);

    $paidCustomer = Customer::factory()->create(['full_name' => 'Paid Up']);
    Invoice::factory()->create([
        'customer_id' => $paidCustomer->id,
        'balance_due' => '0.00',
        'status' => 'paid',
    ]);

    $this->actingAs($user);

    Livewire::test(OutstandingReceivablesWidget::class)
        ->assertCanSeeTableRecords([$bigOwner])
        ->assertCanNotSeeTableRecords([$paidCustomer]);
});

it('DriverLeaderboardWidget shows drivers with completed trips in the last 30 days', function (): void {
    $user = User::factory()->create();
    $user->assignRole('super_admin');

    $busy = Driver::factory()->create(['full_name' => 'Busy Driver']);
    Trip::factory()->create([
        'driver_id' => $busy->id,
        'status' => TripStatus::Completed,
        'scheduled_end' => CarbonImmutable::now()->subDays(5),
    ]);

    $idle = Driver::factory()->create(['full_name' => 'Idle Driver']);

    $this->actingAs($user);

    Livewire::test(DriverLeaderboardWidget::class)
        ->assertCanSeeTableRecords([$busy])
        ->assertCanNotSeeTableRecords([$idle]);
});
