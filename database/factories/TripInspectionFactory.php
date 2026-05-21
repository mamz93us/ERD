<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Enums\FuelLevel;
use App\Enums\TripInspectionStage;
use App\Models\Trip;
use App\Models\TripInspection;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<TripInspection>
 */
class TripInspectionFactory extends Factory
{
    public function definition(): array
    {
        return [
            'trip_id' => Trip::factory(),
            'stage' => TripInspectionStage::Pickup,
            'inspector_user_id' => User::query()->inRandomOrder()->value('id') ?? User::factory(),
            'odometer' => fake()->numberBetween(1000, 200000),
            'fuel_level' => fake()->randomElement(FuelLevel::cases()),
            'damage_marks' => ['version' => 1, 'marks' => []],
            'accessories_checklist' => ['spare_tire' => true, 'jack' => true, 'first_aid' => false],
            'driver_signature_path' => 'signatures/'.fake()->uuid().'.png',
            'performed_at' => now(),
        ];
    }
}
