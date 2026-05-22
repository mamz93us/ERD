<?php

declare(strict_types=1);

namespace App\Services\Invoicing;

use App\Enums\CreditNoteReason;
use App\Enums\CreditNoteStatus;
use App\Enums\InvoiceStatus;
use App\Models\CreditNote;
use App\Models\Invoice;
use App\Models\User;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use InvalidArgumentException;
use RuntimeException;

/**
 * Spec §6 Phase 8: CreditNoteService with approval workflow.
 *
 * Approval rules:
 *  - Notes ≤ APPROVAL_THRESHOLD start as Approved (the creator's accountant+
 *    role is sufficient authority).
 *  - Notes > APPROVAL_THRESHOLD start as PendingApproval and need a
 *    branch_manager or super_admin to call approve().
 *
 * Applying a note reduces the parent invoice's balance_due and may flip the
 * invoice status (paid → if balance drops to 0, partially_paid → otherwise).
 * "Applied" is the final state; the credit note can't be reversed once applied.
 */
class CreditNoteService
{
    /** EGP. Above this threshold, branch_manager+ approval is required. */
    public const APPROVAL_THRESHOLD = '5000.00';

    public function create(
        Invoice $invoice,
        User $createdBy,
        CreditNoteReason $reason,
        string $reasonDetails,
        string $amount,
    ): CreditNote {
        if (bccomp($amount, '0.00', 2) <= 0) {
            throw new InvalidArgumentException('Credit note amount must be greater than zero.');
        }
        if (bccomp($amount, (string) $invoice->balance_due, 2) > 0) {
            throw new InvalidArgumentException(
                "Credit note amount ({$amount}) exceeds invoice balance_due ({$invoice->balance_due})."
            );
        }

        $needsApproval = bccomp($amount, self::APPROVAL_THRESHOLD, 2) > 0;

        return CreditNote::create([
            'invoice_id' => $invoice->id,
            'created_by_user_id' => $createdBy->id,
            'approved_by_user_id' => $needsApproval ? null : $createdBy->id,
            'issue_date' => Carbon::now()->toDateString(),
            'reason' => $reason,
            'reason_details' => $reasonDetails,
            'amount' => $amount,
            'status' => $needsApproval ? CreditNoteStatus::PendingApproval : CreditNoteStatus::Approved,
            'approved_at' => $needsApproval ? null : now(),
        ]);
    }

    public function approve(CreditNote $note, User $approver): CreditNote
    {
        if ($note->status !== CreditNoteStatus::PendingApproval) {
            throw new RuntimeException(
                "Credit note {$note->note_number} is not pending approval (status: {$note->status?->value})."
            );
        }
        if (! $approver->hasAnyRole(['branch_manager', 'super_admin'])) {
            throw new RuntimeException(
                "User {$approver->email} lacks the role required to approve credit notes > "
                .self::APPROVAL_THRESHOLD.' EGP.'
            );
        }

        $note->update([
            'status' => CreditNoteStatus::Approved,
            'approved_by_user_id' => $approver->id,
            'approved_at' => now(),
        ]);

        return $note->fresh();
    }

    public function reject(CreditNote $note, User $rejecter): CreditNote
    {
        if ($note->status !== CreditNoteStatus::PendingApproval) {
            throw new RuntimeException(
                "Credit note {$note->note_number} is not pending approval (status: {$note->status?->value})."
            );
        }

        $note->update([
            'status' => CreditNoteStatus::Rejected,
            'approved_by_user_id' => $rejecter->id,
            'approved_at' => now(),
        ]);

        return $note->fresh();
    }

    /**
     * Apply an approved credit note to its parent invoice. Reduces invoice
     * balance_due by the note amount and adjusts invoice status.
     */
    public function apply(CreditNote $note): CreditNote
    {
        if ($note->status !== CreditNoteStatus::Approved) {
            throw new RuntimeException(
                "Credit note {$note->note_number} must be approved before applying (status: {$note->status?->value})."
            );
        }

        return DB::transaction(function () use ($note) {
            $invoice = Invoice::query()->lockForUpdate()->findOrFail($note->invoice_id);

            $newBalance = bcsub((string) $invoice->balance_due, (string) $note->amount, 2);
            if (bccomp($newBalance, '0.00', 2) < 0) {
                throw new RuntimeException(
                    "Credit note {$note->note_number} would leave invoice balance negative."
                );
            }

            $newStatus = bccomp($newBalance, '0.00', 2) === 0
                ? InvoiceStatus::Paid
                : ($invoice->paid_amount > 0 ? InvoiceStatus::PartiallyPaid : $invoice->status);

            $invoice->update([
                'balance_due' => $newBalance,
                'status' => $newStatus,
            ]);

            $note->update([
                'status' => CreditNoteStatus::Applied,
                'applied_at' => now(),
            ]);

            return $note->fresh();
        });
    }
}
