<?php

declare(strict_types=1);

use App\Enums\CreditNoteReason;
use App\Enums\CreditNoteStatus;
use App\Enums\InvoiceStatus;
use App\Enums\PaymentMethod;
use App\Enums\TripStatus;
use App\Models\CorporateAccount;
use App\Models\Customer;
use App\Models\Invoice;
use App\Models\Payment;
use App\Models\Trip;
use App\Models\User;
use App\Services\Invoicing\CreditNoteService;
use App\Services\Invoicing\InvoiceService;
use App\Services\Invoicing\PaymentService;
use Carbon\CarbonImmutable;
use Database\Seeders\BranchSeeder;
use Database\Seeders\CarCategorySeeder;
use Database\Seeders\RateCardSeeder;
use Database\Seeders\RoleSeeder;

beforeEach(function (): void {
    $this->seed(RoleSeeder::class);
    $this->seed(BranchSeeder::class);
    $this->seed(CarCategorySeeder::class);
    $this->seed(RateCardSeeder::class);
});

/* ============================================================================
 * InvoiceService
 * ========================================================================== */

it('InvoiceService.generateFromTrip creates a draft invoice mirroring trip totals', function (): void {
    $trip = Trip::factory()->create([
        'status' => TripStatus::Completed,
        'subtotal' => '1000.00',
        'vat_amount' => '140.00',
        'total_amount' => '1140.00',
    ]);

    $invoice = app(InvoiceService::class)->generateFromTrip($trip);

    expect($invoice->trip_id)->toBe($trip->id)
        ->and($invoice->customer_id)->toBe($trip->customer_id)
        ->and($invoice->status)->toBe(InvoiceStatus::Draft)
        ->and((string) $invoice->subtotal)->toBe('1000.00')
        ->and((string) $invoice->vat_amount)->toBe('140.00')
        ->and((string) $invoice->total)->toBe('1140.00')
        ->and((string) $invoice->balance_due)->toBe('1140.00')
        ->and($invoice->invoice_number)->toStartWith('INV-')
        ->and($invoice->lines)->toHaveCount(1);
});

it('InvoiceService.generateFromTrip is idempotent — returns existing invoice if trip already invoiced', function (): void {
    $trip = Trip::factory()->create(['status' => TripStatus::Completed]);

    $first = app(InvoiceService::class)->generateFromTrip($trip);
    $second = app(InvoiceService::class)->generateFromTrip($trip);

    expect($second->id)->toBe($first->id);
});

it('InvoiceService.generateFromTrip rejects trips that are not in a billable status', function (): void {
    $trip = Trip::factory()->create(['status' => TripStatus::Draft]);

    expect(fn () => app(InvoiceService::class)->generateFromTrip($trip))
        ->toThrow(InvalidArgumentException::class);
});

it('InvoiceService.generateConsolidatedForCorporate builds one invoice with N lines for the month', function (): void {
    $account = CorporateAccount::factory()->create(['payment_terms_days' => 30, 'discount_percentage' => 0]);
    $customer = Customer::factory()->create(['corporate_account_id' => $account->id]);

    $monthStart = CarbonImmutable::parse('2026-06-01 00:00:00');
    $monthEnd = CarbonImmutable::parse('2026-06-30 23:59:59');

    for ($i = 0; $i < 3; $i++) {
        Trip::factory()->create([
            'customer_id' => $customer->id,
            'corporate_account_id' => $account->id,
            'status' => TripStatus::Completed,
            'scheduled_start' => $monthStart->addDays($i * 5),
            'scheduled_end' => $monthStart->addDays($i * 5)->addHours(8),
            'subtotal' => '500.00',
            'vat_amount' => '70.00',
            'total_amount' => '570.00',
        ]);
    }

    $invoice = app(InvoiceService::class)->generateConsolidatedForCorporate($account, $monthStart, $monthEnd);

    expect($invoice->lines)->toHaveCount(3)
        ->and((string) $invoice->subtotal)->toBe('1500.00')
        ->and((string) $invoice->vat_amount)->toBe('210.00')
        ->and((string) $invoice->total)->toBe('1710.00')
        ->and($invoice->corporate_account_id)->toBe($account->id);
});

it('InvoiceService.generateConsolidatedForCorporate applies corporate discount before VAT', function (): void {
    $account = CorporateAccount::factory()->create(['payment_terms_days' => 30, 'discount_percentage' => '10.00']);
    $customer = Customer::factory()->create(['corporate_account_id' => $account->id]);

    $monthStart = CarbonImmutable::parse('2026-07-01 00:00:00');
    $monthEnd = CarbonImmutable::parse('2026-07-31 23:59:59');

    Trip::factory()->create([
        'customer_id' => $customer->id,
        'corporate_account_id' => $account->id,
        'status' => TripStatus::Completed,
        'scheduled_start' => $monthStart->addDay(),
        'scheduled_end' => $monthStart->addDay()->addHours(8),
        'subtotal' => '1000.00',
        'vat_amount' => '140.00',
        'total_amount' => '1140.00',
    ]);

    $invoice = app(InvoiceService::class)->generateConsolidatedForCorporate($account, $monthStart, $monthEnd);

    // 1000 subtotal − 100 discount = 900 taxable. VAT 14% of 900 = 126. Total = 1026.
    expect((string) $invoice->subtotal)->toBe('1000.00')
        ->and((string) $invoice->discount_amount)->toBe('100.00')
        ->and((string) $invoice->vat_amount)->toBe('126.00')
        ->and((string) $invoice->total)->toBe('1026.00');
});

it('InvoiceService.generateConsolidatedForCorporate throws when no billable trips exist in window', function (): void {
    $account = CorporateAccount::factory()->create();

    expect(fn () => app(InvoiceService::class)->generateConsolidatedForCorporate(
        $account,
        CarbonImmutable::parse('2026-06-01'),
        CarbonImmutable::parse('2026-06-30'),
    ))->toThrow(InvalidArgumentException::class);
});

/* ============================================================================
 * CreditNoteService
 * ========================================================================== */

it('CreditNoteService.create auto-approves notes at or below the 5000 EGP threshold', function (): void {
    $invoice = Invoice::factory()->create(['balance_due' => '10000.00', 'total' => '10000.00']);
    $user = User::factory()->create();

    $note = app(CreditNoteService::class)->create(
        $invoice,
        $user,
        CreditNoteReason::Goodwill,
        'small concession',
        '4500.00',
    );

    expect($note->status)->toBe(CreditNoteStatus::Approved)
        ->and($note->approved_by_user_id)->toBe($user->id)
        ->and($note->approved_at)->not->toBeNull();
});

it('CreditNoteService.create flags notes above 5000 EGP as pending_approval', function (): void {
    $invoice = Invoice::factory()->create(['balance_due' => '20000.00', 'total' => '20000.00']);
    $user = User::factory()->create();

    $note = app(CreditNoteService::class)->create(
        $invoice,
        $user,
        CreditNoteReason::Cancellation,
        'large refund',
        '7500.00',
    );

    expect($note->status)->toBe(CreditNoteStatus::PendingApproval)
        ->and($note->approved_by_user_id)->toBeNull();
});

it('CreditNoteService.approve requires branch_manager+ role', function (): void {
    $invoice = Invoice::factory()->create(['balance_due' => '20000.00', 'total' => '20000.00']);
    $accountant = User::factory()->create();
    $accountant->assignRole('accountant');

    $note = app(CreditNoteService::class)->create($invoice, $accountant, CreditNoteReason::Cancellation, '...', '7500.00');

    expect(fn () => app(CreditNoteService::class)->approve($note, $accountant))
        ->toThrow(RuntimeException::class);

    $manager = User::factory()->create();
    $manager->assignRole('branch_manager');

    $approved = app(CreditNoteService::class)->approve($note, $manager);
    expect($approved->status)->toBe(CreditNoteStatus::Approved)
        ->and($approved->approved_by_user_id)->toBe($manager->id);
});

it('CreditNoteService.apply reduces invoice balance and flips status to paid when zero', function (): void {
    $invoice = Invoice::factory()->create([
        'balance_due' => '1000.00',
        'total' => '1000.00',
        'paid_amount' => '0.00',
    ]);
    $user = User::factory()->create();

    $note = app(CreditNoteService::class)->create($invoice, $user, CreditNoteReason::Goodwill, '...', '1000.00');
    app(CreditNoteService::class)->apply($note);

    $invoice->refresh();
    $note->refresh();
    expect((string) $invoice->balance_due)->toBe('0.00')
        ->and($invoice->status)->toBe(InvoiceStatus::Paid)
        ->and($note->status)->toBe(CreditNoteStatus::Applied)
        ->and($note->applied_at)->not->toBeNull();
});

/* ============================================================================
 * PaymentService
 * ========================================================================== */

it('PaymentService.allocate splits one payment across multiple invoices', function (): void {
    $customer = Customer::factory()->create();
    $invA = Invoice::factory()->create(['customer_id' => $customer->id, 'total' => '500.00', 'balance_due' => '500.00']);
    $invB = Invoice::factory()->create(['customer_id' => $customer->id, 'total' => '300.00', 'balance_due' => '300.00']);

    $payment = Payment::factory()->create([
        'customer_id' => $customer->id,
        'method' => PaymentMethod::BankTransfer,
        'amount' => '800.00',
    ]);

    $allocs = app(PaymentService::class)->allocate($payment, [
        $invA->id => '500.00',
        $invB->id => '300.00',
    ]);

    expect($allocs)->toHaveCount(2);

    $invA->refresh();
    $invB->refresh();
    expect((string) $invA->balance_due)->toBe('0.00')
        ->and($invA->status)->toBe(InvoiceStatus::Paid)
        ->and((string) $invB->balance_due)->toBe('0.00')
        ->and($invB->status)->toBe(InvoiceStatus::Paid);
});

it('PaymentService.allocate marks invoice partially_paid when amount < balance', function (): void {
    $invoice = Invoice::factory()->create(['total' => '1000.00', 'balance_due' => '1000.00']);
    $payment = Payment::factory()->create([
        'customer_id' => $invoice->customer_id,
        'amount' => '400.00',
    ]);

    app(PaymentService::class)->allocate($payment, [$invoice->id => '400.00']);

    $invoice->refresh();
    expect((string) $invoice->paid_amount)->toBe('400.00')
        ->and((string) $invoice->balance_due)->toBe('600.00')
        ->and($invoice->status)->toBe(InvoiceStatus::PartiallyPaid);
});

it('PaymentService.allocate rejects sum greater than payment amount', function (): void {
    $invoice = Invoice::factory()->create(['total' => '500.00', 'balance_due' => '500.00']);
    $payment = Payment::factory()->create(['amount' => '300.00']);

    expect(fn () => app(PaymentService::class)->allocate($payment, [$invoice->id => '500.00']))
        ->toThrow(InvalidArgumentException::class);
});

it('PaymentService.allocate rejects an allocation greater than the invoice balance', function (): void {
    $invoice = Invoice::factory()->create(['total' => '500.00', 'balance_due' => '500.00']);
    $payment = Payment::factory()->create(['amount' => '1000.00']);

    expect(fn () => app(PaymentService::class)->allocate($payment, [$invoice->id => '800.00']))
        ->toThrow(InvalidArgumentException::class);
});

it('PaymentService.allocate prevents double-allocating the same payment to the same invoice', function (): void {
    $invoice = Invoice::factory()->create(['total' => '1000.00', 'balance_due' => '1000.00']);
    $payment = Payment::factory()->create(['amount' => '800.00']);

    app(PaymentService::class)->allocate($payment, [$invoice->id => '400.00']);

    expect(fn () => app(PaymentService::class)->allocate($payment, [$invoice->id => '200.00']))
        ->toThrow(RuntimeException::class);
});

it('Invoice/CreditNote/Payment/VendorBill all auto-generate prefixed numbers', function (): void {
    $invoice = Invoice::factory()->create();
    $payment = Payment::factory()->create();
    $user = User::factory()->create();
    $note = app(CreditNoteService::class)->create($invoice, $user, CreditNoteReason::Goodwill, '...', '100.00');

    expect($invoice->invoice_number)->toStartWith('INV-')
        ->and($payment->payment_number)->toStartWith('P-')
        ->and($note->note_number)->toStartWith('CN-');
});
