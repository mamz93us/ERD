<?php

declare(strict_types=1);

use App\Enums\CarDocumentType;
use App\Enums\CarStatus;
use App\Enums\CarTransmission;
use App\Enums\SubRentalContractStatus;
use App\Models\Branch;
use App\Models\Car;
use App\Models\CarDocument;
use App\Models\Driver;
use App\Models\DriverDocument;
use App\Models\SubRentalContract;
use App\Models\User;
use App\Notifications\DocumentExpired;
use App\Notifications\DocumentExpiringSoon;
use Database\Seeders\BranchSeeder;
use Database\Seeders\CarCategorySeeder;
use Database\Seeders\RoleSeeder;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Notification;

beforeEach(function (): void {
    $this->seed(RoleSeeder::class);
    $this->seed(BranchSeeder::class);
    $this->seed(CarCategorySeeder::class);
});

it('casts car enums correctly', function (): void {
    $car = Car::factory()->create();

    expect($car->transmission)->toBeInstanceOf(CarTransmission::class)
        ->and($car->status)->toBe(CarStatus::Available);
});

it('available() scope returns only cars whose status is available', function (): void {
    Car::factory()->count(2)->create(['status' => CarStatus::Available]);
    Car::factory()->create(['status' => CarStatus::InMaintenance]);
    Car::factory()->create(['status' => CarStatus::OutOfService]);

    expect(Car::query()->available()->count())->toBe(2);
});

it('inMaintenance() scope returns only in-maintenance cars', function (): void {
    Car::factory()->create(['status' => CarStatus::Available]);
    Car::factory()->count(3)->create(['status' => CarStatus::InMaintenance]);

    expect(Car::query()->inMaintenance()->count())->toBe(3);
});

it('documentExpiringWithin($days) finds cars whose active doc expires inside the window', function (): void {
    $expiring = Car::factory()->create();
    CarDocument::factory()->expiringIn(20)->create(['car_id' => $expiring->id]);

    $safe = Car::factory()->create();
    CarDocument::factory()->expiringIn(120)->create(['car_id' => $safe->id]);

    $cars = Car::query()->documentExpiringWithin(30)->pluck('id')->all();

    expect($cars)->toContain($expiring->id)->not->toContain($safe->id);
});

it('hides cars from other branches via the BelongsToBranch scope', function (): void {
    $abh = Branch::query()->where('code', 'ABH')->firstOrFail();
    $cai = Branch::query()->where('code', 'CAI')->firstOrFail();

    Car::factory()->count(2)->create(['branch_id' => $abh->id]);
    Car::factory()->count(3)->create(['branch_id' => $cai->id]);

    $abhManager = User::factory()->create(['branch_id' => $abh->id]);
    $abhManager->assignRole('branch_manager');

    $this->actingAs($abhManager);

    expect(Car::query()->count())->toBe(2);
});

it('CarDocumentObserver deactivates the previous active doc when a new active one of the same type is saved', function (): void {
    $car = Car::factory()->create();

    $oldDoc = CarDocument::factory()->create([
        'car_id' => $car->id,
        'doc_type' => CarDocumentType::RegistrationLicense,
        'is_active' => true,
    ]);

    $newDoc = CarDocument::factory()->create([
        'car_id' => $car->id,
        'doc_type' => CarDocumentType::RegistrationLicense,
        'is_active' => true,
    ]);

    expect($oldDoc->fresh()->is_active)->toBeFalse()
        ->and($newDoc->fresh()->is_active)->toBeTrue();
});

it('CarDocumentObserver does not touch docs of a different type', function (): void {
    $car = Car::factory()->create();

    $registration = CarDocument::factory()->create([
        'car_id' => $car->id,
        'doc_type' => CarDocumentType::RegistrationLicense,
        'is_active' => true,
    ]);

    CarDocument::factory()->create([
        'car_id' => $car->id,
        'doc_type' => CarDocumentType::CompulsoryInsurance,
        'is_active' => true,
    ]);

    expect($registration->fresh()->is_active)->toBeTrue();
});

it('SubRentalContract::coversDateRange returns true only inside the contract window', function (): void {
    $contract = SubRentalContract::factory()->create([
        'start_date' => '2026-05-01',
        'end_date' => '2026-06-30',
        'status' => SubRentalContractStatus::Active,
    ]);

    expect($contract->coversDateRange(Carbon::parse('2026-05-15'), Carbon::parse('2026-06-15')))->toBeTrue()
        ->and($contract->coversDateRange(Carbon::parse('2026-04-25'), Carbon::parse('2026-05-15')))->toBeFalse()
        ->and($contract->coversDateRange(Carbon::parse('2026-06-15'), Carbon::parse('2026-07-15')))->toBeFalse();
});

it('CheckCarDocumentExpiry dispatches DocumentExpiringSoon at the spec windows', function (): void {
    Notification::fake();

    $abh = Branch::query()->where('code', 'ABH')->firstOrFail();
    $fleet = User::factory()->create();
    $fleet->assignRole('fleet_manager');

    $car = Car::factory()->create(['branch_id' => $abh->id]);

    // Doc expiring in exactly 30 days hits one of the WARN_WINDOWS
    CarDocument::factory()->expiringIn(30)->create(['car_id' => $car->id]);
    // Doc expiring in 45 days is in the windows-gap and should NOT notify
    CarDocument::factory()->expiringIn(45)->create([
        'car_id' => $car->id,
        'doc_type' => CarDocumentType::CompulsoryInsurance,
    ]);

    $this->artisan('documents:check-expiry')->assertSuccessful();

    Notification::assertSentTimes(DocumentExpiringSoon::class, 1);
});

it('CheckCarDocumentExpiry marks expired-doc cars out_of_service and dispatches DocumentExpired', function (): void {
    Notification::fake();

    $fleet = User::factory()->create();
    $fleet->assignRole('fleet_manager');

    $car = Car::factory()->create(['status' => CarStatus::Available]);
    CarDocument::factory()->expired()->create(['car_id' => $car->id]);

    $this->artisan('documents:check-expiry')->assertSuccessful();

    expect($car->fresh()->status)->toBe(CarStatus::OutOfService);
    Notification::assertSentTimes(DocumentExpired::class, 1);
});

it('CheckCarDocumentExpiry also sweeps driver_documents', function (): void {
    Notification::fake();

    $abh = Branch::query()->where('code', 'ABH')->firstOrFail();
    $fleet = User::factory()->create();
    $fleet->assignRole('fleet_manager');

    $driver = Driver::factory()->create(['branch_id' => $abh->id]);
    DriverDocument::factory()->create([
        'driver_id' => $driver->id,
        'expiry_date' => now()->addDays(7),
        'is_active' => true,
    ]);

    $this->artisan('documents:check-expiry')->assertSuccessful();

    Notification::assertSentTimes(DocumentExpiringSoon::class, 1);
});

it('renders the cars and sub-rental-contracts index pages for super_admin', function (string $path): void {
    $user = User::factory()->create();
    $user->assignRole('super_admin');

    $this->actingAs($user)->get($path)->assertSuccessful();
})->with(['/admin/cars', '/admin/sub-rental-contracts', '/admin']);
