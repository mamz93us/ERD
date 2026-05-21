<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\PartnerAgency;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<PartnerAgency>
 */
class PartnerAgencyFactory extends Factory
{
    public function definition(): array
    {
        return [
            'name' => fake()->company(),
            'name_ar' => 'وكالة شريكة',
            'contact_person' => fake()->name(),
            'phone' => fake()->phoneNumber(),
            'email' => fake()->unique()->companyEmail(),
            'tax_id' => fake()->numerify('TAX-#########'),
            'address' => fake()->address(),
            'credit_limit' => fake()->randomFloat(2, 0, 100000),
            'payment_terms_days' => fake()->randomElement([0, 15, 30, 45, 60]),
            'is_active' => true,
        ];
    }
}
