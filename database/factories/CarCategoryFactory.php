<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Enums\CarCategoryClass;
use App\Models\CarCategory;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<CarCategory>
 */
class CarCategoryFactory extends Factory
{
    public function definition(): array
    {
        $class = fake()->randomElement(CarCategoryClass::cases());

        return [
            'name' => ucfirst($class->value),
            'name_ar' => 'فئة',
            'class_code' => $class->value,
            'default_seats' => fake()->numberBetween(4, 16),
            'sort_order' => fake()->numberBetween(0, 100),
        ];
    }
}
