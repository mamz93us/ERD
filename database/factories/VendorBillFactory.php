<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Enums\VendorBillStatus;
use App\Enums\VendorType;
use App\Models\VendorBill;
use Carbon\CarbonImmutable;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<VendorBill>
 */
class VendorBillFactory extends Factory
{
    public function definition(): array
    {
        $subtotal = (string) fake()->randomFloat(2, 200, 15000);
        $vat = bcdiv(bcmul($subtotal, '14', 4), '100', 2);
        $total = bcadd($subtotal, $vat, 2);
        $billDate = CarbonImmutable::instance(fake()->dateTimeBetween('-2 months', 'now'));

        return [
            'vendor_type' => VendorType::Fuel,
            'partner_agency_id' => null,
            'garage_id' => null,
            'bill_date' => $billDate->toDateString(),
            'due_date' => $billDate->addDays(30)->toDateString(),
            'subtotal' => $subtotal,
            'vat_amount' => $vat,
            'total' => $total,
            'paid_amount' => '0.00',
            'balance_due' => $total,
            'status' => VendorBillStatus::Draft,
            'description' => fake()->sentence(),
        ];
    }
}
