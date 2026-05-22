<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Enums\ExpenseCategory;
use App\Enums\ExpensePaidBy;
use App\Models\Branch;
use App\Models\Expense;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Expense>
 */
class ExpenseFactory extends Factory
{
    public function definition(): array
    {
        return [
            'branch_id' => Branch::factory(),
            'car_id' => null,
            'driver_id' => null,
            'category' => fake()->randomElement(ExpenseCategory::cases()),
            'amount' => (string) fake()->randomFloat(2, 50, 8000),
            'expense_date' => fake()->date(),
            'paid_by' => fake()->randomElement(ExpensePaidBy::cases()),
            'paid_by_user_id' => User::factory(),
            'description' => fake()->sentence(),
        ];
    }
}
