<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Enums\PaymentMethod;
use App\Models\Branch;
use App\Models\Customer;
use App\Models\Payment;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Payment>
 */
class PaymentFactory extends Factory
{
    public function definition(): array
    {
        return [
            'customer_id' => Customer::factory(),
            'corporate_account_id' => null,
            'method' => fake()->randomElement(PaymentMethod::cases()),
            'amount' => (string) fake()->randomFloat(2, 500, 25000),
            'payment_date' => fake()->date(),
            'reference_number' => fake()->bothify('TXN-########'),
            'received_by_user_id' => User::factory(),
            'branch_id' => Branch::factory(),
            'is_reconciled' => false,
        ];
    }
}
