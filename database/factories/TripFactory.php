<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Enums\TripStatus;
use App\Models\Branch;
use App\Models\Car;
use App\Models\Customer;
use App\Models\Driver;
use App\Models\RateCard;
use App\Models\Trip;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Trip>
 */
class TripFactory extends Factory
{
    public function definition(): array
    {
        $branch = Branch::query()->inRandomOrder()->first() ?? Branch::factory()->create();
        $car = Car::factory()->create(['branch_id' => $branch->id]);
        $driver = Driver::factory()->create(['branch_id' => $branch->id]);
        $rateCard = RateCard::query()
            ->where('category_id', $car->category_id)
            ->first()
            ?? RateCard::factory()->create(['category_id' => $car->category_id]);

        $start = fake()->dateTimeBetween('+1 day', '+30 days');
        $end = (clone $start)->modify('+'.fake()->numberBetween(2, 5).' days');

        return [
            'branch_id' => $branch->id,
            'customer_id' => Customer::factory(),
            'corporate_account_id' => null,
            'car_id' => $car->id,
            'driver_id' => $driver->id,
            'quotation_id' => null,
            'rate_card_id' => $rateCard->id,
            'scheduled_start' => $start,
            'scheduled_end' => $end,
            'pickup_location' => fake()->address(),
            'dropoff_location' => fake()->address(),
            'status' => TripStatus::Draft,
            'subtotal' => fake()->randomFloat(2, 1000, 20000),
            'vat_amount' => 0,
            'total_amount' => 0,
            'notes' => null,
        ];
    }
}
