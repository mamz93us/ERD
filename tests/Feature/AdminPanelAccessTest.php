<?php

declare(strict_types=1);

use App\Models\User;
use Database\Seeders\RoleSeeder;
use Filament\Facades\Filament;

beforeEach(function (): void {
    $this->seed(RoleSeeder::class);
});

it('redirects unauthenticated visitors to the admin login', function (): void {
    $response = $this->get('/admin');
    $response->assertRedirect('/admin/login');
});

it('lets an active user access the admin panel', function (): void {
    $user = User::factory()->create(['is_active' => true]);
    $user->assignRole('super_admin');

    expect($user->canAccessPanel(Filament::getPanel('admin')))->toBeTrue();
});

it('blocks an inactive user from the admin panel even with a role', function (): void {
    $user = User::factory()->create(['is_active' => false]);
    $user->assignRole('super_admin');

    expect($user->canAccessPanel(Filament::getPanel('admin')))->toBeFalse();
});
