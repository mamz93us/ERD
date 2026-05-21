<?php

declare(strict_types=1);

use App\Enums\EgyptianGovernorate;
use App\Filament\Admin\Resources\Quotations\QuotationResource;
use App\Models\CarCategory;
use App\Models\Customer;
use App\Models\RateCard;
use Database\Seeders\CarCategorySeeder;
use Database\Seeders\RateCardSeeder;

beforeEach(function (): void {
    $this->seed(CarCategorySeeder::class);
    $this->seed(RateCardSeeder::class);
});

it('exposes 27 Egyptian governorates', function (): void {
    expect(EgyptianGovernorate::cases())->toHaveCount(27);
});

it('Quotation applyPricing adds cross_city surcharge when pickup governorate ≠ dropoff governorate', function (): void {
    $category = CarCategory::query()->where('class_code', 'economy')->firstOrFail();
    $customer = Customer::factory()->create();

    $base = [
        'category_id' => $category->id,
        'customer_id' => $customer->id,
        'corporate_account_id' => null,
        'pickup_at' => '2026-06-01 09:00:00',
        'dropoff_at' => '2026-06-02 09:00:00',
        'estimated_distance_km' => 0,
    ];

    $sameGovernorate = QuotationResource::applyPricing(array_merge($base, [
        'pickup_location' => EgyptianGovernorate::Cairo->value,
        'dropoff_location' => EgyptianGovernorate::Cairo->value,
    ]));

    $differentGovernorate = QuotationResource::applyPricing(array_merge($base, [
        'pickup_location' => EgyptianGovernorate::Cairo->value,
        'dropoff_location' => EgyptianGovernorate::Alexandria->value,
    ]));

    expect((float) $differentGovernorate['subtotal'])
        ->toBeGreaterThan((float) $sameGovernorate['subtotal']);
});

it('Quotation applyPricing does NOT add cross_city when both governorates are the same', function (): void {
    $category = CarCategory::query()->where('class_code', 'economy')->firstOrFail();
    $customer = Customer::factory()->create();

    $defaultCard = RateCard::query()
        ->where('category_id', $category->id)->whereNull('corporate_account_id')->firstOrFail();

    $result = QuotationResource::applyPricing([
        'category_id' => $category->id,
        'customer_id' => $customer->id,
        'corporate_account_id' => null,
        'pickup_at' => '2026-06-01 09:00:00',
        'dropoff_at' => '2026-06-02 09:00:00',
        'estimated_distance_km' => 0,
        'pickup_location' => EgyptianGovernorate::Giza->value,
        'dropoff_location' => EgyptianGovernorate::Giza->value,
    ]);

    // 1 day base + driver allowance (no cross_city surcharge)
    $expectedSubtotal = (float) $defaultCard->daily_rate + (float) $defaultCard->driver_daily_allowance;

    expect((float) $result['subtotal'])->toBe(round($expectedSubtotal, 2));
});
