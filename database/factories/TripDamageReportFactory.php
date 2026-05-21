<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Enums\TripDamageReportStatus;
use App\Models\Trip;
use App\Models\TripDamageReport;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<TripDamageReport>
 */
class TripDamageReportFactory extends Factory
{
    public function definition(): array
    {
        return [
            'trip_id' => Trip::factory(),
            'description' => fake()->sentence(),
            'damage_area' => ['side' => 'front_left', 'severity' => 'scratch'],
            'repair_cost_estimate' => fake()->randomFloat(2, 100, 5000),
            'charged_to_customer' => false,
            'status' => TripDamageReportStatus::Reported,
        ];
    }
}
