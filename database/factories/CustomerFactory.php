<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Enums\CustomerType;
use App\Enums\PreferredLanguage;
use App\Models\Customer;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Customer>
 */
class CustomerFactory extends Factory
{
    public function definition(): array
    {
        return [
            'corporate_account_id' => null,
            'type' => CustomerType::Individual,
            'full_name' => fake()->name(),
            'full_name_ar' => 'عميل',
            'phone' => fake()->phoneNumber(),
            'whatsapp_phone' => null,
            'email' => fake()->unique()->safeEmail(),
            'national_id' => fake()->numerify('##############'),
            'address' => fake()->address(),
            'preferred_language' => PreferredLanguage::Ar,
            'loyalty_points' => 0,
            'is_blacklisted' => false,
            'blacklist_reason' => null,
            'notes' => null,
        ];
    }
}
