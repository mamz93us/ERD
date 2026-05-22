<?php

declare(strict_types=1);

namespace App\Services\Invoicing;

use App\Enums\InvoiceStatus;
use App\Enums\TripStatus;
use App\Models\CorporateAccount;
use App\Models\Invoice;
use App\Models\InvoiceLine;
use App\Models\Trip;
use Carbon\CarbonImmutable;
use Illuminate\Support\Facades\DB;
use InvalidArgumentException;

/**
 * Spec §6 Phase 8: InvoiceService.
 *
 *  - generateFromTrip(Trip): Invoice — one invoice for one completed trip.
 *  - generateConsolidatedForCorporate(CorporateAccount, monthStart, monthEnd): Invoice
 *    — one invoice with N lines, one line per completed trip in the window, for
 *    a corporate account's monthly billing run.
 *
 * Money math is bcmath-based — decimal(15,2) in, string-decimal out, never float.
 * The trip itself already has subtotal/vat_amount/total_amount computed at quote
 * time (PricingService); we trust those numbers and just port them onto the
 * invoice + line. If the trip needs re-pricing, that's a separate flow.
 */
class InvoiceService
{
    private const VAT_RATE = '14.00';

    /**
     * Generate a draft invoice from a single trip. The trip must be in a billable
     * status (completed/closed/invoiced). Idempotent: if an invoice already exists
     * for this trip (single-trip invoice), returns it unchanged.
     */
    public function generateFromTrip(Trip $trip): Invoice
    {
        if (! in_array($trip->status, [TripStatus::Completed, TripStatus::Invoiced, TripStatus::Closed], true)) {
            throw new InvalidArgumentException(
                "Trip {$trip->trip_number} status is {$trip->status?->value}; must be completed/invoiced/closed to invoice."
            );
        }

        $existing = Invoice::query()
            ->where('trip_id', $trip->id)
            ->whereNull('deleted_at')
            ->first();

        if ($existing !== null) {
            return $existing;
        }

        return DB::transaction(function () use ($trip) {
            $issueDate = CarbonImmutable::now();
            $dueDate = $this->dueDateFor($trip->corporateAccount, $issueDate);

            $subtotal = (string) $trip->subtotal;
            $vat = (string) $trip->vat_amount;
            $total = (string) $trip->total_amount;

            $invoice = Invoice::create([
                'customer_id' => $trip->customer_id,
                'corporate_account_id' => $trip->corporate_account_id,
                'trip_id' => $trip->id,
                'issue_date' => $issueDate->toDateString(),
                'due_date' => $dueDate->toDateString(),
                'subtotal' => $subtotal,
                'vat_amount' => $vat,
                'discount_amount' => '0.00',
                'total' => $total,
                'paid_amount' => '0.00',
                'balance_due' => $total,
                'currency' => 'EGP',
                'status' => InvoiceStatus::Draft,
            ]);

            InvoiceLine::create([
                'invoice_id' => $invoice->id,
                'description' => sprintf(
                    'Trip %s: %s → %s',
                    $trip->trip_number,
                    $trip->pickup_location,
                    $trip->dropoff_location,
                ),
                'quantity' => '1.00',
                'unit_price' => $subtotal,
                'discount_amount' => '0.00',
                'vat_rate' => self::VAT_RATE,
                'vat_amount' => $vat,
                'line_total' => $total,
                'trip_id' => $trip->id,
                'sort_order' => 0,
            ]);

            return $invoice->fresh('lines');
        });
    }

    /**
     * Generate a consolidated draft invoice for a corporate account covering all
     * billable trips in [monthStart, monthEnd]. One line per trip.
     *
     * Throws if no billable trips found in the window. Applies the corporate
     * account's discount_percentage to the subtotal before VAT.
     */
    public function generateConsolidatedForCorporate(
        CorporateAccount $account,
        CarbonImmutable $monthStart,
        CarbonImmutable $monthEnd,
    ): Invoice {
        $trips = Trip::query()
            ->withoutGlobalScopes()
            ->where('corporate_account_id', $account->id)
            ->whereIn('status', [TripStatus::Completed, TripStatus::Closed])
            ->whereBetween('scheduled_end', [$monthStart, $monthEnd])
            ->whereDoesntHave('invoices', function ($q) {
                $q->whereNull('deleted_at');
            })
            ->orderBy('scheduled_start')
            ->get();

        if ($trips->isEmpty()) {
            throw new InvalidArgumentException(
                "No un-invoiced completed trips for {$account->company_name} between "
                ."{$monthStart->toDateString()} and {$monthEnd->toDateString()}."
            );
        }

        return DB::transaction(function () use ($account, $monthStart, $monthEnd, $trips) {
            $issueDate = CarbonImmutable::now();
            $dueDate = $this->dueDateFor($account, $issueDate);

            $subtotal = '0.00';
            foreach ($trips as $trip) {
                $subtotal = bcadd($subtotal, (string) $trip->subtotal, 2);
            }

            $discountPct = (string) ($account->discount_percentage ?? '0');
            $discountAmount = bcdiv(bcmul($subtotal, $discountPct, 4), '100', 2);
            $taxable = bcsub($subtotal, $discountAmount, 2);
            $vat = bcdiv(bcmul($taxable, self::VAT_RATE, 4), '100', 2);
            $total = bcadd($taxable, $vat, 2);

            $invoice = Invoice::create([
                'customer_id' => $trips->first()->customer_id,
                'corporate_account_id' => $account->id,
                'trip_id' => null,
                'issue_date' => $issueDate->toDateString(),
                'due_date' => $dueDate->toDateString(),
                'subtotal' => $subtotal,
                'vat_amount' => $vat,
                'discount_amount' => $discountAmount,
                'total' => $total,
                'paid_amount' => '0.00',
                'balance_due' => $total,
                'currency' => 'EGP',
                'status' => InvoiceStatus::Draft,
                'notes' => sprintf(
                    'Consolidated billing for %s — %s to %s (%d trips)',
                    $account->company_name,
                    $monthStart->toDateString(),
                    $monthEnd->toDateString(),
                    $trips->count(),
                ),
            ]);

            $sort = 0;
            foreach ($trips as $trip) {
                InvoiceLine::create([
                    'invoice_id' => $invoice->id,
                    'description' => sprintf(
                        'Trip %s (%s): %s → %s',
                        $trip->trip_number,
                        $trip->scheduled_start?->toDateString(),
                        $trip->pickup_location,
                        $trip->dropoff_location,
                    ),
                    'quantity' => '1.00',
                    'unit_price' => (string) $trip->subtotal,
                    'discount_amount' => '0.00',
                    'vat_rate' => self::VAT_RATE,
                    'vat_amount' => (string) $trip->vat_amount,
                    'line_total' => (string) $trip->total_amount,
                    'trip_id' => $trip->id,
                    'sort_order' => $sort++,
                ]);
            }

            return $invoice->fresh('lines');
        });
    }

    private function dueDateFor(?CorporateAccount $account, CarbonImmutable $issueDate): CarbonImmutable
    {
        $days = $account?->payment_terms_days ?? 14;

        return $issueDate->addDays((int) $days);
    }
}
