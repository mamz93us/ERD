<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\Garage;
use Illuminate\Database\Seeder;

class GarageSeeder extends Seeder
{
    public function run(): void
    {
        Garage::query()->updateOrCreate(
            ['name' => 'Adly Group Internal Garage'],
            [
                'phone' => null,
                'address' => null,
                'is_internal' => true,
                'specialties' => ['oil', 'brakes', 'engine', 'transmission', 'ac', 'bodywork'],
                'is_active' => true,
            ]
        );
    }
}
