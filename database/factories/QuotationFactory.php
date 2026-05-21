<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Enums\QuotationStatus;
use App\Models\CarCategory;
use App\Models\Customer;
use App\Models\Quotation;
use App\Models\RateCard;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Quotation>
 */
class QuotationFactory extends Factory
{
    public function definition(): array
    {
        $pickup = fake()->dateTimeBetween('+1 day', '+30 days');
        $dropoff = (clone $pickup)->modify('+'.fake()->numberBetween(2, 10).' days');
        $category = CarCategory::query()->inRandomOrder()->first() ?? CarCategory::factory()->create();
        $rateCard = RateCard::query()->where('category_id', $category->id)->first() ?? RateCard::factory()->create(['category_id' => $category->id]);

        return [
            'customer_id' => Customer::factory(),
            'corporate_account_id' => null,
            'created_by_user_id' => User::query()->inRandomOrder()->value('id') ?? User::factory(),
            'pickup_at' => $pickup,
            'dropoff_at' => $dropoff,
            'pickup_location' => fake()->city(),
            'dropoff_location' => fake()->city(),
            'estimated_distance_km' => fake()->numberBetween(50, 2000),
            'category_id' => $category->id,
            'rate_card_id' => $rateCard->id,
            'subtotal' => fake()->randomFloat(2, 500, 20000),
            'vat_amount' => 0,
            'total_amount' => 0,
            'valid_until' => now()->addDays(7),
            'status' => QuotationStatus::Draft,
            'notes' => null,
            'terms_and_conditions' => null,
        ];
    }
}
