<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Enums\MaintenanceItemType;
use App\Models\MaintenanceItem;
use App\Models\MaintenanceOrder;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<MaintenanceItem>
 */
class MaintenanceItemFactory extends Factory
{
    public function definition(): array
    {
        $quantity = fake()->randomFloat(2, 1, 5);
        $unitCost = fake()->randomFloat(2, 50, 800);

        return [
            'maintenance_order_id' => MaintenanceOrder::factory(),
            'item_type' => fake()->randomElement(MaintenanceItemType::cases()),
            'description' => fake()->sentence(3),
            'quantity' => $quantity,
            'unit_cost' => $unitCost,
            'total_cost' => round($quantity * $unitCost, 2),
        ];
    }
}
