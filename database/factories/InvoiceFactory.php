<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Enums\InvoiceStatus;
use App\Models\Customer;
use App\Models\Invoice;
use Carbon\CarbonImmutable;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Invoice>
 */
class InvoiceFactory extends Factory
{
    public function definition(): array
    {
        $subtotal = (string) fake()->randomFloat(2, 500, 50000);
        $vat = bcdiv(bcmul($subtotal, '14', 4), '100', 2);
        $total = bcadd($subtotal, $vat, 2);
        $issueDate = CarbonImmutable::instance(fake()->dateTimeBetween('-2 months', 'now'));

        return [
            'customer_id' => Customer::factory(),
            'corporate_account_id' => null,
            'trip_id' => null,
            'issue_date' => $issueDate->toDateString(),
            'due_date' => $issueDate->addDays(14)->toDateString(),
            'subtotal' => $subtotal,
            'vat_amount' => $vat,
            'discount_amount' => '0.00',
            'total' => $total,
            'paid_amount' => '0.00',
            'balance_due' => $total,
            'currency' => 'EGP',
            'status' => InvoiceStatus::Draft,
        ];
    }
}
