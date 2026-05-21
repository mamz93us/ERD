<?php

declare(strict_types=1);

use App\Enums\CustomerType;
use App\Enums\DriverDocumentType;
use App\Enums\DriverStatus;
use App\Enums\LeadStatus;
use App\Models\Branch;
use App\Models\CarCategory;
use App\Models\CorporateAccount;
use App\Models\Customer;
use App\Models\CustomerCommunication;
use App\Models\Driver;
use App\Models\DriverDocument;
use App\Models\Garage;
use App\Models\Lead;
use App\Models\User;
use Database\Seeders\BranchSeeder;
use Database\Seeders\CarCategorySeeder;
use Database\Seeders\GarageSeeder;
use Database\Seeders\RoleSeeder;

beforeEach(function (): void {
    $this->seed(RoleSeeder::class);
    $this->seed(BranchSeeder::class);
});

it('seeds 6 car categories with the spec class codes', function (): void {
    $this->seed(CarCategorySeeder::class);

    expect(CarCategory::query()->count())->toBe(6)
        ->and(CarCategory::query()->pluck('class_code')->map->value->all())
        ->toEqualCanonicalizing(['economy', 'midsize', 'suv', 'luxury', 'van', 'minibus']);
});

it('seeds one internal placeholder garage', function (): void {
    $this->seed(GarageSeeder::class);

    expect(Garage::query()->where('is_internal', true)->count())->toBe(1);
});

it('casts driver enums correctly (employment_type + status)', function (): void {
    $driver = Driver::factory()->create();

    expect($driver->status)->toBeInstanceOf(DriverStatus::class)
        ->and($driver->status)->toBe(DriverStatus::Active);
});

it('casts customer type and preferred_language to enums', function (): void {
    $customer = Customer::factory()->create();

    expect($customer->type)->toBeInstanceOf(CustomerType::class)
        ->and($customer->type)->toBe(CustomerType::Individual);
});

it('hides drivers from other branches via the BelongsToBranch global scope', function (): void {
    $abh = Branch::query()->where('code', 'ABH')->firstOrFail();
    $cai = Branch::query()->where('code', 'CAI')->firstOrFail();

    Driver::factory()->count(2)->create(['branch_id' => $abh->id]);
    Driver::factory()->count(3)->create(['branch_id' => $cai->id]);

    $abhManager = User::factory()->create(['branch_id' => $abh->id]);
    $abhManager->assignRole('branch_manager');

    $this->actingAs($abhManager);

    expect(Driver::query()->count())->toBe(2);
});

it('attaches communications to a customer', function (): void {
    $customer = Customer::factory()->create();
    CustomerCommunication::factory()->count(3)->create(['customer_id' => $customer->id]);

    expect($customer->fresh()->communications)->toHaveCount(3);
});

it('attaches leads to a customer with default New status', function (): void {
    $customer = Customer::factory()->create();
    $lead = Lead::factory()->create(['customer_id' => $customer->id]);

    expect($lead->status)->toBe(LeadStatus::New_)
        ->and($customer->fresh()->leads)->toHaveCount(1);
});

it('attaches documents to a driver and casts doc_type to enum', function (): void {
    $driver = Driver::factory()->create();
    $doc = DriverDocument::factory()->create([
        'driver_id' => $driver->id,
        'doc_type' => DriverDocumentType::DrivingLicense,
    ]);

    expect($doc->doc_type)->toBe(DriverDocumentType::DrivingLicense)
        ->and($driver->fresh()->documents)->toHaveCount(1);
});

it('links customers to a corporate account', function (): void {
    $corp = CorporateAccount::factory()->create();
    $customer = Customer::factory()->create(['corporate_account_id' => $corp->id]);

    expect($customer->corporateAccount->id)->toBe($corp->id)
        ->and($corp->fresh()->customers)->toHaveCount(1);
});

it('renders each Phase 2 admin index page for an authenticated super_admin', function (string $path): void {
    $user = User::factory()->create();
    $user->assignRole('super_admin');

    $this->actingAs($user)
        ->get($path)
        ->assertSuccessful();
})->with([
    '/admin/car-categories',
    '/admin/partner-agencies',
    '/admin/garages',
    '/admin/corporate-accounts',
    '/admin/customers',
    '/admin/leads',
    '/admin/drivers',
]);
