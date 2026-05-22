<?php

declare(strict_types=1);

namespace App\Enums;

use Filament\Support\Contracts\HasLabel;

enum CreditNoteReason: string implements HasLabel
{
    case Cancellation = 'cancellation';
    case ServiceComplaint = 'service_complaint';
    case Goodwill = 'goodwill';
    case DepositReturn = 'deposit_return';
    case BillingError = 'billing_error';
    case Other = 'other';

    public function getLabel(): string
    {
        return __("enums.credit_note_reason.{$this->value}");
    }
}
