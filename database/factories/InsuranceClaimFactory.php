<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Enums\InsuranceClaimStatus;
use App\Models\Car;
use App\Models\InsuranceClaim;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<InsuranceClaim>
 */
class InsuranceClaimFactory extends Factory
{
    public function definition(): array
    {
        return [
            'car_id' => Car::factory(),
            'trip_id' => null,
            'claim_number' => 'IC-'.fake()->unique()->numerify('########'),
            'incident_date' => fake()->dateTimeBetween('-6 months', 'now'),
            'incident_location' => fake()->city(),
            'description' => fake()->paragraph(),
            'police_report_number' => fake()->optional()->numerify('PR-########'),
            'claim_amount' => fake()->randomFloat(2, 1000, 100000),
            'status' => InsuranceClaimStatus::Reported,
            'documents' => null,
            'notes' => null,
        ];
    }
}
