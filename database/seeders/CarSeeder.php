<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Enums\CarDocumentType;
use App\Models\Car;
use App\Models\CarDocument;
use Illuminate\Database\Seeder;

/**
 * Seeds a small demo fleet (5 owned cars across the seeded branches and categories).
 * Production deployments should not run this seeder; it exists for local dev so the
 * panel has data to browse.
 */
class CarSeeder extends Seeder
{
    public function run(): void
    {
        Car::factory()->count(5)->create()->each(function (Car $car): void {
            CarDocument::factory()->create([
                'car_id' => $car->id,
                'doc_type' => CarDocumentType::RegistrationLicense,
                'expiry_date' => now()->addYear(),
                'is_active' => true,
            ]);
            CarDocument::factory()->create([
                'car_id' => $car->id,
                'doc_type' => CarDocumentType::CompulsoryInsurance,
                'expiry_date' => now()->addMonths(6),
                'is_active' => true,
            ]);
        });
    }
}
