<?php

declare(strict_types=1);

namespace App\Enums;

use Filament\Support\Contracts\HasColor;
use Filament\Support\Contracts\HasLabel;

enum InsuranceClaimStatus: string implements HasColor, HasLabel
{
    case Reported = 'reported';
    case Submitted = 'submitted';
    case UnderReview = 'under_review';
    case Approved = 'approved';
    case Rejected = 'rejected';
    case Paid = 'paid';

    public function getLabel(): string
    {
        return __("enums.insurance_claim_status.{$this->value}");
    }

    public function getColor(): string
    {
        return match ($this) {
            self::Reported => 'gray',
            self::Submitted, self::UnderReview => 'info',
            self::Approved => 'warning',
            self::Rejected => 'danger',
            self::Paid => 'success',
        };
    }
}
