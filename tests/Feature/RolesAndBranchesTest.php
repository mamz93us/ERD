<?php

declare(strict_types=1);

use App\Models\Branch;
use Database\Seeders\BranchSeeder;
use Database\Seeders\RoleSeeder;
use Spatie\Permission\Models\Role;

beforeEach(function (): void {
    $this->seed(RoleSeeder::class);
    $this->seed(BranchSeeder::class);
});

it('seeds the seven required roles', function (): void {
    expect(Role::query()->count())->toBe(7)
        ->and(Role::query()->pluck('name')->all())
        ->toContain(
            'super_admin',
            'branch_manager',
            'dispatcher',
            'accountant',
            'reservations_agent',
            'driver_supervisor',
            'fleet_manager',
        );
});

it('seeds the ABH and CAI branches with bilingual names', function (): void {
    expect(Branch::query()->count())->toBe(2);

    $abh = Branch::query()->where('code', 'ABH')->firstOrFail();
    $cai = Branch::query()->where('code', 'CAI')->firstOrFail();

    expect($abh->name_ar)->toBe('أبو حماد')
        ->and($cai->name_ar)->toBe('القاهرة');
});
