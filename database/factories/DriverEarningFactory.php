<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\Driver;
use App\Models\DriverEarning;
use App\Models\Trip;
use Carbon\CarbonImmutable;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<DriverEarning>
 */
class DriverEarningFactory extends Factory
{
    public function definition(): array
    {
        $gross = (string) fake()->randomFloat(2, 100, 5000);
        $monthStart = CarbonImmutable::today()->startOfMonth();

        return [
            'driver_id' => Driver::factory(),
            'trip_id' => Trip::factory(),
            'gross_commission' => $gross,
            'deductions' => [],
            'net_payable' => $gross,
            'pay_period_start' => $monthStart->toDateString(),
            'pay_period_end' => $monthStart->endOfMonth()->toDateString(),
        ];
    }
}
