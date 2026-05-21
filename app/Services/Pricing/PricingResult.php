<?php

declare(strict_types=1);

namespace App\Services\Pricing;

/**
 * Immutable result of PricingService::calculate(). All money fields are floats
 * rounded to 2 decimals — converted to decimal(15,2) when persisted to DB.
 *
 * @phpstan-type Surcharge array{label: string, amount: float}
 */
final readonly class PricingResult
{
    /**
     * @param  list<Surcharge>  $surcharges
     */
    public function __construct(
        public string $rateCardId,
        public int $durationHours,
        public int $billedDays,
        public int $billedExtraHours,
        public float $baseAmount,
        public int $includedKm,
        public int $extraKm,
        public float $extraKmCharge,
        public float $driverAllowance,
        public array $surcharges,
        public float $subtotal,
        public float $vatRate,
        public float $vatAmount,
        public float $total,
    ) {}

    /** @return array<string,mixed> */
    public function toArray(): array
    {
        return [
            'rate_card_id' => $this->rateCardId,
            'duration_hours' => $this->durationHours,
            'billed_days' => $this->billedDays,
            'billed_extra_hours' => $this->billedExtraHours,
            'base_amount' => $this->baseAmount,
            'included_km' => $this->includedKm,
            'extra_km' => $this->extraKm,
            'extra_km_charge' => $this->extraKmCharge,
            'driver_allowance' => $this->driverAllowance,
            'surcharges' => $this->surcharges,
            'subtotal' => $this->subtotal,
            'vat_rate' => $this->vatRate,
            'vat_amount' => $this->vatAmount,
            'total' => $this->total,
        ];
    }
}
