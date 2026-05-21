<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Enums\MaintenanceServiceType;
use App\Models\Car;
use App\Models\MaintenanceSchedule;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<MaintenanceSchedule>
 */
class MaintenanceScheduleFactory extends Factory
{
    public function definition(): array
    {
        return [
            'car_id' => Car::factory(),
            'service_type' => fake()->randomElement(MaintenanceServiceType::cases()),
            'interval_km' => fake()->randomElement([5000, 10000, 15000, 20000]),
            'interval_days' => fake()->randomElement([90, 180, 365]),
            'last_done_km' => null,
            'last_done_date' => null,
            'is_active' => true,
        ];
    }
}
