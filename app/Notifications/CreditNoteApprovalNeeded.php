<?php

declare(strict_types=1);

namespace App\Notifications;

use App\Models\CreditNote;

class CreditNoteApprovalNeeded extends TemplatedNotification
{
    protected string $templateKey = 'credit_note_approval_needed';

    public function __construct(public readonly CreditNote $note) {}

    protected function vars(object $notifiable): array
    {
        return [
            'manager_name' => $notifiable->full_name ?? '',
            'note_number' => $this->note->note_number,
            'invoice_number' => $this->note->invoice?->invoice_number ?? '',
            'amount' => number_format((float) $this->note->amount, 2).' EGP',
            'reason' => $this->note->reason?->getLabel() ?? '',
            'created_by' => $this->note->createdBy?->full_name ?? '',
        ];
    }

    protected function databasePayload(): array
    {
        return [
            'note_id' => $this->note->id,
            'note_number' => $this->note->note_number,
            'amount' => (string) $this->note->amount,
        ];
    }
}
