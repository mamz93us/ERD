<?php

declare(strict_types=1);

use App\Models\CarCategory;
use App\Models\CorporateAccount;
use App\Models\RateCard;
use App\Services\Pricing\PricingService;
use Carbon\CarbonImmutable;

beforeEach(function (): void {
    $this->category = CarCategory::factory()->create([
        'name' => 'Test',
        'class_code' => 'economy',
        'default_seats' => 5,
    ]);

    $this->defaultCard = RateCard::factory()->create([
        'category_id' => $this->category->id,
        'corporate_account_id' => null,
        'hourly_rate' => 100,
        'daily_rate' => 1000,
        'weekly_rate' => 6000,
        'monthly_rate' => 22000,
        'included_km_per_day' => 100,
        'extra_km_rate' => 2,
        'extra_hour_rate' => 50,
        'driver_daily_allowance' => 150,
        'cross_city_surcharge' => 300,
        'effective_from' => now()->subMonth(),
        'effective_to' => null,
        'is_active' => true,
    ]);

    $this->service = new PricingService;
});

it('throws when end is not after start', function (): void {
    $start = CarbonImmutable::parse('2026-06-01 10:00');
    expect(fn () => $this->service->calculate(
        $this->category->id, null, $start, $start, 50
    ))->toThrow(InvalidArgumentException::class);
});

it('throws when no rate card matches the category', function (): void {
    $other = CarCategory::factory()->create(['class_code' => 'luxury']);
    $start = CarbonImmutable::parse('2026-06-01 10:00');
    $end = $start->addHours(4);

    expect(fn () => $this->service->calculate($other->id, null, $start, $end, 50))
        ->toThrow(RuntimeException::class);
});

it('charges hourly for sub-day rentals', function (): void {
    $start = CarbonImmutable::parse('2026-06-01 10:00');
    $end = $start->addHours(4);

    $result = $this->service->calculate($this->category->id, null, $start, $end, 0);

    expect($result->durationHours)->toBe(4)
        ->and($result->billedDays)->toBe(1)
        ->and($result->billedExtraHours)->toBe(4)
        ->and($result->baseAmount)->toBe(400.0);  // 4h × 100/h
});

it('charges by day for 1–26 day rentals (daily wins over weekly when shorter than a week)', function (): void {
    $start = CarbonImmutable::parse('2026-06-01 09:00');
    $end = $start->addDays(3);

    $result = $this->service->calculate($this->category->id, null, $start, $end, 0);

    expect($result->billedDays)->toBe(3)
        ->and($result->baseAmount)->toBe(3000.0);  // 3 × 1000
});

it('picks weekly + daily mix when cheaper than pure daily', function (): void {
    // 10 days. Pure daily = 10 × 1000 = 10000. Weekly+daily = 1 × 6000 + 3 × 1000 = 9000. Mix wins.
    $start = CarbonImmutable::parse('2026-06-01 09:00');
    $end = $start->addDays(10);

    $result = $this->service->calculate($this->category->id, null, $start, $end, 0);

    expect($result->billedDays)->toBe(10)
        ->and($result->baseAmount)->toBe(9000.0);
});

it('picks monthly when 30+ days', function (): void {
    // 30 days. Pure daily = 30k. Pure weekly = 4 × 6000 + 2 × 1000 = 26000. Monthly = 1 × 22000. Monthly wins.
    $start = CarbonImmutable::parse('2026-06-01 09:00');
    $end = $start->addDays(30);

    $result = $this->service->calculate($this->category->id, null, $start, $end, 0);

    expect($result->billedDays)->toBe(30)
        ->and($result->baseAmount)->toBe(22000.0);
});

it('adds extra-km charge when km exceeds included_km × days', function (): void {
    $start = CarbonImmutable::parse('2026-06-01 09:00');
    $end = $start->addDays(2);  // included = 2 × 100 = 200km

    $result = $this->service->calculate($this->category->id, null, $start, $end, 350);

    expect($result->includedKm)->toBe(200)
        ->and($result->extraKm)->toBe(150)
        ->and($result->extraKmCharge)->toBe(300.0);  // 150 × 2
});

it('adds driver daily allowance', function (): void {
    $start = CarbonImmutable::parse('2026-06-01 09:00');
    $end = $start->addDays(5);

    $result = $this->service->calculate($this->category->id, null, $start, $end, 0);

    expect($result->driverAllowance)->toBe(750.0);  // 5 × 150
});

it('adds cross_city surcharge when flagged', function (): void {
    $start = CarbonImmutable::parse('2026-06-01 09:00');
    $end = $start->addDays(2);

    $without = $this->service->calculate($this->category->id, null, $start, $end, 0);
    $with = $this->service->calculate($this->category->id, null, $start, $end, 0, [], ['cross_city']);

    expect($with->subtotal - $without->subtotal)->toBe(300.0);
});

it('sums caller-supplied addons into surcharges', function (): void {
    $start = CarbonImmutable::parse('2026-06-01 09:00');
    $end = $start->addDays(1);
    $addons = [
        ['label' => 'child_seat', 'amount' => 100],
        ['label' => 'gps', 'amount' => 50],
    ];

    $result = $this->service->calculate($this->category->id, null, $start, $end, 0, $addons);

    expect($result->surcharges)->toHaveCount(2)
        ->and(array_sum(array_column($result->surcharges, 'amount')))->toBe(150.0);
});

it('applies the configured VAT rate to the subtotal', function (): void {
    $start = CarbonImmutable::parse('2026-06-01 09:00');
    $end = $start->addDays(1);

    $result = $this->service->calculate($this->category->id, null, $start, $end, 0);

    expect($result->vatRate)->toBe(0.14)
        ->and($result->vatAmount)->toBe(round($result->subtotal * 0.14, 2))
        ->and($result->total)->toBe(round($result->subtotal + $result->vatAmount, 2));
});

it('prefers a corporate-specific rate card when one exists', function (): void {
    $corp = CorporateAccount::factory()->create();
    $corpCard = RateCard::factory()->create([
        'category_id' => $this->category->id,
        'corporate_account_id' => $corp->id,
        'daily_rate' => 700,  // discounted
        'included_km_per_day' => 100,
        'extra_km_rate' => 2,
        'driver_daily_allowance' => 0,
        'cross_city_surcharge' => 0,
        'effective_from' => now()->subMonth(),
        'is_active' => true,
    ]);

    $start = CarbonImmutable::parse('2026-06-01 09:00');
    $end = $start->addDays(2);

    $defaultResult = $this->service->calculate($this->category->id, null, $start, $end, 0);
    $corpResult = $this->service->calculate($this->category->id, $corp->id, $start, $end, 0);

    expect($defaultResult->rateCardId)->toBe($this->defaultCard->id)
        ->and($corpResult->rateCardId)->toBe($corpCard->id)
        ->and($corpResult->baseAmount)->toBe(1400.0);  // 2 × 700
});

it('falls back to the default rate card when corporate has no card for the category', function (): void {
    $corp = CorporateAccount::factory()->create();
    // No corporate-specific card for this category

    $start = CarbonImmutable::parse('2026-06-01 09:00');
    $end = $start->addDays(1);

    $result = $this->service->calculate($this->category->id, $corp->id, $start, $end, 0);

    expect($result->rateCardId)->toBe($this->defaultCard->id);
});

it('skips inactive rate cards', function (): void {
    $this->defaultCard->update(['is_active' => false]);

    $start = CarbonImmutable::parse('2026-06-01 09:00');
    $end = $start->addDays(1);

    expect(fn () => $this->service->calculate($this->category->id, null, $start, $end, 0))
        ->toThrow(RuntimeException::class);
});
