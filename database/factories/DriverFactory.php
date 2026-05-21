<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Enums\DriverStatus;
use App\Enums\EmploymentType;
use App\Models\Branch;
use App\Models\Driver;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Driver>
 */
class DriverFactory extends Factory
{
    public function definition(): array
    {
        return [
            'branch_id' => Branch::query()->inRandomOrder()->value('id') ?? Branch::factory(),
            'national_id' => fake()->unique()->numerify('##############'),
            'full_name' => fake()->name('male'),
            'full_name_ar' => 'سائق',
            'phone' => fake()->phoneNumber(),
            'whatsapp_phone' => fake()->phoneNumber(),
            'address' => fake()->address(),
            'date_of_birth' => fake()->dateTimeBetween('-65 years', '-22 years'),
            'hire_date' => fake()->dateTimeBetween('-5 years', '-1 month'),
            'employment_type' => fake()->randomElement(EmploymentType::cases()),
            'base_salary' => fake()->randomFloat(2, 3000, 12000),
            'trip_commission_percentage' => fake()->randomFloat(2, 5, 25),
            'status' => DriverStatus::Active,
            'rating' => fake()->randomFloat(2, 3.0, 5.0),
            'notes' => null,
        ];
    }
}
