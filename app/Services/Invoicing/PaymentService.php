<?php

declare(strict_types=1);

namespace App\Services\Invoicing;

use App\Enums\InvoiceStatus;
use App\Models\Invoice;
use App\Models\Payment;
use App\Models\PaymentAllocation;
use Illuminate\Support\Facades\DB;
use InvalidArgumentException;
use RuntimeException;

/**
 * Spec §6 Phase 8: PaymentService.allocate(Payment, [invoiceId => amount]).
 *
 * One payment can be split across multiple invoices (corporate paying off
 * several invoices in one transfer). One invoice can receive multiple payments.
 * The `payment_allocations` table is the many-to-many bridge.
 *
 * Constraints enforced inside one DB transaction with row locks on each invoice:
 *  - Sum of allocations <= payment.amount (left-over stays unallocated).
 *  - Each allocation amount <= that invoice's current balance_due.
 *  - An invoice can't be over-paid past balance_due even via a fresh allocation.
 *  - On success: invoice.paid_amount += amount, balance_due -= amount,
 *    status flips to PartiallyPaid or Paid as appropriate.
 *
 * Re-allocating to an invoice that already has an allocation from this payment
 * is forbidden (unique (payment_id, invoice_id) — caller must aggregate first).
 */
class PaymentService
{
    /**
     * @param  array<string, string>  $allocations  [invoiceId => allocatedAmount]
     * @return list<PaymentAllocation>
     */
    public function allocate(Payment $payment, array $allocations): array
    {
        if ($allocations === []) {
            throw new InvalidArgumentException('No allocations provided.');
        }

        $sum = '0.00';
        foreach ($allocations as $invoiceId => $amount) {
            if (bccomp($amount, '0.00', 2) <= 0) {
                throw new InvalidArgumentException("Allocation amount for invoice {$invoiceId} must be > 0.");
            }
            $sum = bcadd($sum, $amount, 2);
        }

        $alreadyAllocated = $payment->allocatedTotal();
        $available = bcsub((string) $payment->amount, $alreadyAllocated, 2);
        if (bccomp($sum, $available, 2) > 0) {
            throw new InvalidArgumentException(
                "Allocation sum ({$sum}) exceeds payment unallocated balance ({$available})."
            );
        }

        return DB::transaction(function () use ($payment, $allocations) {
            $created = [];

            foreach ($allocations as $invoiceId => $amount) {
                $invoice = Invoice::query()->lockForUpdate()->find($invoiceId);
                if ($invoice === null) {
                    throw new InvalidArgumentException("Invoice {$invoiceId} not found.");
                }

                if (PaymentAllocation::query()
                    ->where('payment_id', $payment->id)
                    ->where('invoice_id', $invoice->id)
                    ->exists()
                ) {
                    throw new RuntimeException(
                        "Payment {$payment->payment_number} already has an allocation to invoice "
                        ."{$invoice->invoice_number}. Aggregate amounts caller-side."
                    );
                }

                if (bccomp($amount, (string) $invoice->balance_due, 2) > 0) {
                    throw new InvalidArgumentException(
                        "Allocation ({$amount}) exceeds invoice {$invoice->invoice_number} "
                        ."balance_due ({$invoice->balance_due})."
                    );
                }

                $created[] = PaymentAllocation::create([
                    'payment_id' => $payment->id,
                    'invoice_id' => $invoice->id,
                    'allocated_amount' => $amount,
                    'allocated_at' => now(),
                ]);

                $newPaid = bcadd((string) $invoice->paid_amount, $amount, 2);
                $newBalance = bcsub((string) $invoice->balance_due, $amount, 2);
                $newStatus = bccomp($newBalance, '0.00', 2) === 0
                    ? InvoiceStatus::Paid
                    : InvoiceStatus::PartiallyPaid;

                $invoice->update([
                    'paid_amount' => $newPaid,
                    'balance_due' => $newBalance,
                    'status' => $newStatus,
                ]);
            }

            return $created;
        });
    }
}
