<?php

declare(strict_types=1);

namespace App\Enums;

use Filament\Support\Contracts\HasColor;
use Filament\Support\Contracts\HasLabel;

enum CreditNoteStatus: string implements HasColor, HasLabel
{
    case Draft = 'draft';
    case PendingApproval = 'pending_approval';
    case Approved = 'approved';
    case Applied = 'applied';
    case Rejected = 'rejected';

    public function getLabel(): string
    {
        return __("enums.credit_note_status.{$this->value}");
    }

    public function getColor(): string
    {
        return match ($this) {
            self::Draft => 'gray',
            self::PendingApproval => 'warning',
            self::Approved => 'info',
            self::Applied => 'success',
            self::Rejected => 'danger',
        };
    }
}
