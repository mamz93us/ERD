<?php

declare(strict_types=1);

use App\Models\SystemSetting;
use App\Models\User;
use Database\Seeders\BranchSeeder;
use Database\Seeders\RoleSeeder;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Crypt;

beforeEach(function (): void {
    $this->seed(RoleSeeder::class);
    $this->seed(BranchSeeder::class);
    Cache::flush();
});

it('renders the home landing page with three portal tiles', function (): void {
    $this->get('/')
        ->assertOk()
        ->assertSee('/admin', false)
        ->assertSee('/driver', false)
        ->assertSee('/portal', false);
});

it('SystemSetting::get returns the default when the key is missing', function (): void {
    expect(SystemSetting::get('system.name', 'fallback'))->toBe('fallback');
});

it('SystemSetting::put stores plain text for non-secret keys', function (): void {
    SystemSetting::put('system.name', 'Adly Group Agency');

    $row = SystemSetting::where('key', 'system.name')->first();
    expect($row->is_encrypted)->toBeFalse()
        ->and($row->value)->toBe('Adly Group Agency');
    Cache::flush();
    expect(SystemSetting::get('system.name'))->toBe('Adly Group Agency');
});

it('SystemSetting::put encrypts the mail password at rest', function (): void {
    SystemSetting::put('mail.password', 'super-secret');

    $row = SystemSetting::where('key', 'mail.password')->first();
    expect($row->is_encrypted)->toBeTrue()
        ->and($row->value)->not->toBe('super-secret');                 // ciphertext on disk
    expect(Crypt::decryptString($row->value))->toBe('super-secret');   // round-trips
    Cache::flush();
    expect(SystemSetting::get('mail.password'))->toBe('super-secret');  // accessor decrypts
});

it('SystemSetting::put encrypts the WhatsApp token at rest', function (): void {
    SystemSetting::put('whatsapp.token', 'abc-xyz-token');

    $row = SystemSetting::where('key', 'whatsapp.token')->first();
    expect($row->is_encrypted)->toBeTrue()
        ->and($row->value)->not->toBe('abc-xyz-token');
});

it('SystemSetting::get is resilient when the table does not exist yet', function (): void {
    Schema::dropIfExists('system_settings');

    expect(SystemSetting::get('system.name', 'default-on-fresh-install'))
        ->toBe('default-on-fresh-install');
});

it('the admin settings page is accessible to super_admin', function (): void {
    $admin = User::factory()->create();
    $admin->assignRole('super_admin');

    $this->actingAs($admin)
        ->get('/admin/system-settings')
        ->assertSuccessful();
});

it('the audit log page is accessible to super_admin', function (): void {
    $admin = User::factory()->create();
    $admin->assignRole('super_admin');

    $this->actingAs($admin)
        ->get('/admin/audit-log')
        ->assertSuccessful();
});

it('the roles list is accessible to super_admin', function (): void {
    $admin = User::factory()->create();
    $admin->assignRole('super_admin');

    $this->actingAs($admin)
        ->get('/admin/roles')
        ->assertSuccessful();
});
