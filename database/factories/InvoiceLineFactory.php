<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\Invoice;
use App\Models\InvoiceLine;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<InvoiceLine>
 */
class InvoiceLineFactory extends Factory
{
    public function definition(): array
    {
        $qty = '1.00';
        $unit = (string) fake()->randomFloat(2, 100, 5000);
        $subtotal = bcmul($qty, $unit, 2);
        $vat = bcdiv(bcmul($subtotal, '14', 4), '100', 2);

        return [
            'invoice_id' => Invoice::factory(),
            'description' => fake()->sentence(),
            'quantity' => $qty,
            'unit_price' => $unit,
            'discount_amount' => '0.00',
            'vat_rate' => '14.00',
            'vat_amount' => $vat,
            'line_total' => bcadd($subtotal, $vat, 2),
            'trip_id' => null,
            'sort_order' => 0,
        ];
    }
}
