<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AdminUserSeeder extends Seeder
{
    public function run(): void
    {
        $admin = User::query()->updateOrCreate(
            ['email' => 'admin@adlygroup.local'],
            [
                'full_name' => 'Adly Group Admin',
                'full_name_ar' => 'مسؤول مجموعة عدلي',
                'phone' => null,
                'password' => Hash::make('password'),
                'is_active' => true,
                'preferred_locale' => 'ar',
                'email_verified_at' => now(),
                'branch_id' => null,
            ]
        );

        if (! $admin->hasRole('super_admin')) {
            $admin->assignRole('super_admin');
        }
    }
}
