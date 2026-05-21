<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Enums\SubRentalContractStatus;
use App\Models\Car;
use App\Models\PartnerAgency;
use App\Models\SubRentalContract;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<SubRentalContract>
 */
class SubRentalContractFactory extends Factory
{
    public function definition(): array
    {
        $start = fake()->dateTimeBetween('-2 months', '-1 day');

        return [
            'partner_agency_id' => PartnerAgency::query()->inRandomOrder()->value('id') ?? PartnerAgency::factory(),
            'car_id' => Car::factory()->subRented(),
            'start_date' => $start,
            'end_date' => fake()->dateTimeBetween('+1 day', '+6 months'),
            'daily_cost' => fake()->randomFloat(2, 200, 2000),
            'included_km_per_day' => fake()->randomElement([150, 200, 300, 500]),
            'extra_km_cost' => fake()->randomFloat(2, 0.5, 5),
            'terms' => fake()->paragraph(),
            'status' => SubRentalContractStatus::Active,
            'contract_file_path' => null,
        ];
    }
}
