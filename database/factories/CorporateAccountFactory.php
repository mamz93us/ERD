<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\CorporateAccount;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<CorporateAccount>
 */
class CorporateAccountFactory extends Factory
{
    public function definition(): array
    {
        return [
            'company_name' => fake()->company(),
            'company_name_ar' => 'شركة',
            'tax_id' => fake()->numerify('TAX-#########'),
            'commercial_register' => fake()->numerify('CR-#########'),
            'industry' => fake()->randomElement(['hospitality', 'logistics', 'tourism', 'finance', 'oil_gas', 'other']),
            'address' => fake()->address(),
            'billing_email' => fake()->unique()->companyEmail(),
            'billing_phone' => fake()->phoneNumber(),
            'credit_limit' => fake()->randomFloat(2, 5000, 200000),
            'payment_terms_days' => fake()->randomElement([15, 30, 45]),
            'discount_percentage' => fake()->randomFloat(2, 0, 15),
            'is_active' => true,
            'notes' => null,
        ];
    }
}
