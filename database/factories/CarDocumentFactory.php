<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Enums\CarDocumentType;
use App\Models\Car;
use App\Models\CarDocument;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<CarDocument>
 */
class CarDocumentFactory extends Factory
{
    public function definition(): array
    {
        return [
            'car_id' => Car::factory(),
            'doc_type' => fake()->randomElement(CarDocumentType::cases()),
            'document_number' => fake()->numerify('CD-########'),
            'issue_date' => fake()->dateTimeBetween('-3 years', '-1 month'),
            'expiry_date' => fake()->dateTimeBetween('+1 month', '+3 years'),
            'issuer' => fake()->company(),
            'cost' => fake()->optional()->randomFloat(2, 100, 5000),
            'file_path' => null,
            'is_active' => true,
        ];
    }

    public function expiringIn(int $days): self
    {
        return $this->state(fn () => ['expiry_date' => now()->addDays($days)]);
    }

    public function expired(): self
    {
        return $this->state(fn () => ['expiry_date' => now()->subDays(1)]);
    }
}
