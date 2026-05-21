<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Enums\TripExpenseType;
use App\Models\Trip;
use App\Models\TripExpense;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<TripExpense>
 */
class TripExpenseFactory extends Factory
{
    public function definition(): array
    {
        return [
            'trip_id' => Trip::factory(),
            'type' => fake()->randomElement(TripExpenseType::cases()),
            'amount' => fake()->randomFloat(2, 20, 1500),
            'reimbursed' => false,
            'notes' => null,
            'incurred_at' => fake()->dateTimeBetween('-3 days', 'now'),
        ];
    }
}
