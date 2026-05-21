<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\CarCategory;
use App\Models\RateCard;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<RateCard>
 */
class RateCardFactory extends Factory
{
    public function definition(): array
    {
        return [
            'category_id' => CarCategory::query()->inRandomOrder()->value('id') ?? CarCategory::factory(),
            'corporate_account_id' => null,
            'name' => fake()->randomElement(['Standard', 'Promo', 'Seasonal']).' '.fake()->numerify('R-####'),
            'hourly_rate' => fake()->randomFloat(2, 50, 200),
            'daily_rate' => fake()->randomFloat(2, 500, 2000),
            'weekly_rate' => fake()->randomFloat(2, 3000, 12000),
            'monthly_rate' => fake()->randomFloat(2, 12000, 40000),
            'included_km_per_day' => fake()->randomElement([100, 150, 200, 300]),
            'extra_km_rate' => fake()->randomFloat(2, 1, 5),
            'extra_hour_rate' => fake()->randomFloat(2, 30, 100),
            'driver_daily_allowance' => fake()->randomFloat(2, 100, 500),
            'cross_city_surcharge' => fake()->randomFloat(2, 100, 1000),
            'effective_from' => now()->subMonths(1),
            'effective_to' => null,
            'is_active' => true,
        ];
    }
}
