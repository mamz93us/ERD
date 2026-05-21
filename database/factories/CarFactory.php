<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Enums\CarFuelType;
use App\Enums\CarOwnershipType;
use App\Enums\CarStatus;
use App\Enums\CarTransmission;
use App\Models\Branch;
use App\Models\Car;
use App\Models\CarCategory;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Car>
 */
class CarFactory extends Factory
{
    public function definition(): array
    {
        $makes = [
            'Toyota' => ['Corolla', 'Camry', 'Hiace', 'Fortuner'],
            'Hyundai' => ['Elantra', 'Tucson', 'Sonata', 'H1'],
            'Mercedes-Benz' => ['E-Class', 'S-Class', 'Vito'],
            'Kia' => ['Sportage', 'Sorento', 'Carnival'],
            'Mitsubishi' => ['Pajero', 'Outlander'],
        ];

        $make = array_rand($makes);
        $model = fake()->randomElement($makes[$make]);

        return [
            'branch_id' => Branch::query()->inRandomOrder()->value('id') ?? Branch::factory(),
            'category_id' => CarCategory::query()->inRandomOrder()->value('id') ?? CarCategory::factory(),
            'plate' => strtoupper(fake()->unique()->bothify('???-####')),
            'vin' => fake()->boolean(80) ? fake()->unique()->bothify('?#?#?#?#?#?#?#?#?') : null,
            'make' => $make,
            'model' => $model,
            'year' => fake()->numberBetween(2018, (int) date('Y')),
            'color' => fake()->safeColorName(),
            'transmission' => fake()->randomElement(CarTransmission::cases()),
            'fuel_type' => fake()->randomElement(CarFuelType::cases()),
            'seats' => fake()->randomElement([4, 5, 7, 9, 14]),
            'ownership_type' => CarOwnershipType::Owned,
            'status' => CarStatus::Available,
            'current_odometer' => fake()->numberBetween(0, 200_000),
            'acquisition_date' => fake()->dateTimeBetween('-5 years', 'now'),
            'acquisition_cost' => fake()->randomFloat(2, 100_000, 1_500_000),
            'notes' => null,
        ];
    }

    public function subRented(): self
    {
        return $this->state(fn () => ['ownership_type' => CarOwnershipType::SubRented]);
    }
}
