<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Enums\TrafficFinePaymentStatus;
use App\Models\Car;
use App\Models\TrafficFine;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<TrafficFine>
 */
class TrafficFineFactory extends Factory
{
    public function definition(): array
    {
        return [
            'car_id' => Car::factory(),
            'driver_id' => null,
            'trip_id' => null,
            'violation_number' => 'V-'.fake()->unique()->numerify('########'),
            'violation_date' => fake()->dateTimeBetween('-3 months', 'now'),
            'violation_type' => fake()->randomElement(['speeding', 'wrong_parking', 'red_light', 'no_seatbelt', 'phone_use']),
            'location' => fake()->city(),
            'amount' => fake()->randomFloat(2, 100, 5000),
            'payment_status' => TrafficFinePaymentStatus::Unpaid,
            'deducted_from_driver' => false,
        ];
    }
}
