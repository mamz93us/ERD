<?php

declare(strict_types=1);

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;

class RoleSeeder extends Seeder
{
    public const ROLES = [
        'super_admin',
        'branch_manager',
        'dispatcher',
        'accountant',
        'reservations_agent',
        'driver_supervisor',
        'fleet_manager',
    ];

    public function run(): void
    {
        app(PermissionRegistrar::class)->forgetCachedPermissions();

        foreach (self::ROLES as $name) {
            Role::query()->firstOrCreate([
                'name' => $name,
                'guard_name' => 'web',
            ]);
        }
    }
}
