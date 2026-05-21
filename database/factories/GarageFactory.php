<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\Garage;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Garage>
 */
class GarageFactory extends Factory
{
    public function definition(): array
    {
        return [
            'name' => fake()->company().' Auto Service',
            'phone' => fake()->phoneNumber(),
            'address' => fake()->address(),
            'is_internal' => false,
            'specialties' => fake()->randomElements(
                ['oil', 'brakes', 'engine', 'transmission', 'ac', 'bodywork'],
                fake()->numberBetween(1, 4),
            ),
            'is_active' => true,
        ];
    }
}
