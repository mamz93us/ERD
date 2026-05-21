<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Enums\DriverDocumentType;
use App\Models\Driver;
use App\Models\DriverDocument;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<DriverDocument>
 */
class DriverDocumentFactory extends Factory
{
    public function definition(): array
    {
        return [
            'driver_id' => Driver::factory(),
            'doc_type' => fake()->randomElement(DriverDocumentType::cases()),
            'document_number' => fake()->numerify('DOC-########'),
            'issue_date' => fake()->dateTimeBetween('-3 years', '-1 month'),
            'expiry_date' => fake()->dateTimeBetween('+1 month', '+3 years'),
            'issuer' => fake()->company(),
            'file_path' => null,
            'is_active' => true,
        ];
    }
}
