<?php

declare(strict_types=1);

use App\Models\Branch;
use App\Models\User;
use Database\Seeders\BranchSeeder;
use Database\Seeders\RoleSeeder;

beforeEach(function (): void {
    $this->seed(RoleSeeder::class);
    $this->seed(BranchSeeder::class);
});

it('hides other branches data from non-super_admin users via global scope', function (): void {
    $abh = Branch::query()->where('code', 'ABH')->firstOrFail();
    $cai = Branch::query()->where('code', 'CAI')->firstOrFail();

    $abhUser = User::factory()->create(['branch_id' => $abh->id, 'email' => 'abh-mgr@x']);
    $abhUser->assignRole('branch_manager');

    $caiUser = User::factory()->create(['branch_id' => $cai->id, 'email' => 'cai-mgr@x']);
    $caiUser->assignRole('branch_manager');

    $this->actingAs($abhUser);

    $visible = User::query()->pluck('id')->all();
    expect($visible)->toContain($abhUser->id)
        ->and($visible)->not->toContain($caiUser->id);
});

it('lets super_admin see every branch', function (): void {
    $abh = Branch::query()->where('code', 'ABH')->firstOrFail();
    $cai = Branch::query()->where('code', 'CAI')->firstOrFail();

    User::factory()->create(['branch_id' => $abh->id, 'email' => 'abh1@x']);
    User::factory()->create(['branch_id' => $cai->id, 'email' => 'cai1@x']);

    $superAdmin = User::factory()->create(['branch_id' => null, 'email' => 'sa@x']);
    $superAdmin->assignRole('super_admin');

    $this->actingAs($superAdmin);

    expect(User::query()->count())->toBe(3);
});

it('auto-fills branch_id on create from the authenticated user', function (): void {
    $abh = Branch::query()->where('code', 'ABH')->firstOrFail();
    $manager = User::factory()->create(['branch_id' => $abh->id, 'email' => 'mgr@x']);
    $manager->assignRole('branch_manager');

    $this->actingAs($manager);

    // Creating a new user without explicit branch_id should inherit the manager's
    $created = User::factory()->create(['email' => 'auto@x']);

    expect($created->branch_id)->toBe($abh->id);
});
