<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\CarCategory;
use App\Models\RateCard;
use Illuminate\Database\Seeder;

/**
 * One default rate card per car category (no corporate_account_id = default rate).
 * Pricing per category roughly matches Egyptian chauffeur-driven rental market 2026.
 */
class RateCardSeeder extends Seeder
{
    /** @var array<string, array<string, int|float>> */
    private const DEFAULTS = [
        'economy' => ['hourly' => 75, 'daily' => 800, 'weekly' => 5000, 'monthly' => 18000, 'km' => 150, 'extra_km' => 2, 'allowance' => 150, 'cross_city' => 200],
        'midsize' => ['hourly' => 100, 'daily' => 1100, 'weekly' => 6800, 'monthly' => 24000, 'km' => 200, 'extra_km' => 2.5, 'allowance' => 200, 'cross_city' => 300],
        'suv' => ['hourly' => 150, 'daily' => 1700, 'weekly' => 10500, 'monthly' => 36000, 'km' => 200, 'extra_km' => 3, 'allowance' => 250, 'cross_city' => 400],
        'luxury' => ['hourly' => 250, 'daily' => 3000, 'weekly' => 18500, 'monthly' => 65000, 'km' => 200, 'extra_km' => 5, 'allowance' => 350, 'cross_city' => 500],
        'van' => ['hourly' => 180, 'daily' => 2000, 'weekly' => 12500, 'monthly' => 44000, 'km' => 250, 'extra_km' => 3.5, 'allowance' => 300, 'cross_city' => 450],
        'minibus' => ['hourly' => 220, 'daily' => 2500, 'weekly' => 15500, 'monthly' => 55000, 'km' => 300, 'extra_km' => 4, 'allowance' => 350, 'cross_city' => 500],
    ];

    public function run(): void
    {
        foreach (CarCategory::query()->get() as $category) {
            $defaults = self::DEFAULTS[$category->class_code->value] ?? null;
            if ($defaults === null) {
                continue;
            }

            RateCard::query()->updateOrCreate(
                [
                    'category_id' => $category->id,
                    'corporate_account_id' => null,
                    'name' => 'Default - '.$category->name,
                ],
                [
                    'hourly_rate' => $defaults['hourly'],
                    'daily_rate' => $defaults['daily'],
                    'weekly_rate' => $defaults['weekly'],
                    'monthly_rate' => $defaults['monthly'],
                    'included_km_per_day' => $defaults['km'],
                    'extra_km_rate' => $defaults['extra_km'],
                    'extra_hour_rate' => $defaults['hourly'] / 2,
                    'driver_daily_allowance' => $defaults['allowance'],
                    'cross_city_surcharge' => $defaults['cross_city'],
                    'effective_from' => now()->startOfYear(),
                    'effective_to' => null,
                    'is_active' => true,
                ]
            );
        }
    }
}
