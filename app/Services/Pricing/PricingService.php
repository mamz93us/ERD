<?php

declare(strict_types=1);

namespace App\Services\Pricing;

use App\Models\RateCard;
use Carbon\CarbonImmutable;
use InvalidArgumentException;
use RuntimeException;

/**
 * Pure pricing service. No DB writes, no side effects — picks the best matching
 * rate card and returns a PricingResult. Used by Phase 4 QuotationResource (live
 * preview), Phase 5 TripResource (initial pricing on booking), Phase 8 InvoiceService.
 *
 * Pricing rules (spec §5.5 + §6 Phase 4):
 *  - Pick best active rate card: corporate-specific overrides default for category.
 *  - Choose best base period (monthly > weekly > daily > hourly) — minimizes total.
 *  - Included km = billed_days * included_km_per_day.
 *  - Extra km = max(0, estimated_km - included_km) * extra_km_rate.
 *  - Driver daily allowance = billed_days * allowance.
 *  - Optional surcharges (cross_city, etc.) added as named lines.
 *  - VAT = subtotal * config('billing.vat_rate') (default 14%).
 *  - Caller-supplied addons appended to surcharges.
 */
class PricingService
{
    /**
     * @param  list<array{label: string, amount: float|int}>  $addons
     * @param  list<string>  $surchargeFlags  e.g. ['cross_city']
     */
    public function calculate(
        string $categoryId,
        ?string $corporateAccountId,
        CarbonImmutable $start,
        CarbonImmutable $end,
        int $estimatedKm,
        array $addons = [],
        array $surchargeFlags = [],
    ): PricingResult {
        if ($end->lte($start)) {
            throw new InvalidArgumentException('End time must be after start time.');
        }
        if ($estimatedKm < 0) {
            throw new InvalidArgumentException('Estimated km cannot be negative.');
        }

        $card = RateCard::pickFor($categoryId, $corporateAccountId);
        if ($card === null) {
            throw new RuntimeException("No active rate card for category {$categoryId}".
                ($corporateAccountId !== null ? " (corporate {$corporateAccountId})" : '').'.');
        }

        // Duration in whole hours, rounded up.
        $durationHours = (int) ceil($start->diffInMinutes($end) / 60);

        // Bill in days when the rental spans at least one full day; otherwise hourly.
        $billedDays = (int) ceil($durationHours / 24);
        $billedExtraHours = $durationHours < 24 ? $durationHours : 0;

        $baseAmount = $billedExtraHours > 0
            ? $this->bestHourlyOrShortBase($card, $billedExtraHours)
            : $this->bestPeriodicBase($card, $billedDays);

        $includedKm = $billedDays * (int) $card->included_km_per_day;
        $extraKm = max(0, $estimatedKm - $includedKm);
        $extraKmCharge = round($extraKm * (float) $card->extra_km_rate, 2);

        $driverAllowance = round($billedDays * (float) $card->driver_daily_allowance, 2);

        // Surcharges (cross_city flag + caller-supplied addons)
        $surcharges = [];
        if (in_array('cross_city', $surchargeFlags, true) && (float) $card->cross_city_surcharge > 0) {
            $surcharges[] = ['label' => 'cross_city', 'amount' => round((float) $card->cross_city_surcharge, 2)];
        }
        foreach ($addons as $addon) {
            $surcharges[] = ['label' => $addon['label'], 'amount' => round((float) $addon['amount'], 2)];
        }
        $surchargesTotal = array_sum(array_column($surcharges, 'amount'));

        $subtotal = round($baseAmount + $extraKmCharge + $driverAllowance + $surchargesTotal, 2);

        $vatRate = (float) config('billing.vat_rate', 0.14);
        $vatAmount = round($subtotal * $vatRate, 2);
        $total = round($subtotal + $vatAmount, 2);

        return new PricingResult(
            rateCardId: $card->id,
            durationHours: $durationHours,
            billedDays: $billedDays,
            billedExtraHours: $billedExtraHours,
            baseAmount: $baseAmount,
            includedKm: $includedKm,
            extraKm: $extraKm,
            extraKmCharge: $extraKmCharge,
            driverAllowance: $driverAllowance,
            surcharges: $surcharges,
            subtotal: $subtotal,
            vatRate: $vatRate,
            vatAmount: $vatAmount,
            total: $total,
        );
    }

    private function bestHourlyOrShortBase(RateCard $card, int $hours): float
    {
        // For < 24h, hourly is the only option per spec.
        return round($hours * (float) $card->hourly_rate, 2);
    }

    /**
     * For multi-day rentals, choose the cheapest combination of monthly/weekly/daily.
     * Greedy: peel off as many full months as fit, then weeks, then days.
     * Includes a fall-through "daily-only" calculation and picks the minimum.
     */
    private function bestPeriodicBase(RateCard $card, int $days): float
    {
        $monthly = (float) $card->monthly_rate;
        $weekly = (float) $card->weekly_rate;
        $daily = (float) $card->daily_rate;

        $candidates = [];

        // Daily-only baseline
        if ($daily > 0) {
            $candidates[] = $days * $daily;
        }

        // Monthly + weekly + daily greedy
        if ($monthly > 0) {
            $months = intdiv($days, 30);
            $remainder = $days - ($months * 30);
            $weeks = $weekly > 0 ? intdiv($remainder, 7) : 0;
            $remainder -= $weeks * 7;
            $extraDays = $remainder;
            $candidates[] = ($months * $monthly) + ($weeks * $weekly) + ($extraDays * $daily);
        }

        // Weekly + daily greedy
        if ($weekly > 0) {
            $weeks = intdiv($days, 7);
            $extraDays = $days - ($weeks * 7);
            $candidates[] = ($weeks * $weekly) + ($extraDays * $daily);
        }

        return round(min($candidates), 2);
    }
}
