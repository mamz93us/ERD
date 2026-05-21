<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Enums\MaintenanceOrderStatus;
use App\Enums\MaintenanceOrderType;
use App\Models\Car;
use App\Models\Garage;
use App\Models\MaintenanceOrder;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<MaintenanceOrder>
 */
class MaintenanceOrderFactory extends Factory
{
    public function definition(): array
    {
        $start = fake()->dateTimeBetween('+1 day', '+30 days');
        $end = (clone $start)->modify('+'.fake()->numberBetween(2, 8).' hours');

        return [
            'car_id' => Car::factory(),
            'garage_id' => Garage::query()->inRandomOrder()->value('id') ?? Garage::factory(),
            'order_type' => MaintenanceOrderType::Preventive,
            'description' => fake()->sentence(),
            'scheduled_start' => $start,
            'scheduled_end' => $end,
            'subtotal' => 0,
            'vat_amount' => 0,
            'total_cost' => 0,
            'status' => MaintenanceOrderStatus::Scheduled,
        ];
    }
}
