<?php

declare(strict_types=1);

namespace App\Enums;

use Filament\Support\Contracts\HasColor;
use Filament\Support\Contracts\HasLabel;

enum VendorBillStatus: string implements HasColor, HasLabel
{
    case Draft = 'draft';
    case Received = 'received';
    case PartiallyPaid = 'partially_paid';
    case Paid = 'paid';
    case Disputed = 'disputed';

    public function getLabel(): string
    {
        return __("enums.vendor_bill_status.{$this->value}");
    }

    public function getColor(): string
    {
        return match ($this) {
            self::Draft => 'gray',
            self::Received => 'info',
            self::PartiallyPaid => 'warning',
            self::Paid => 'success',
            self::Disputed => 'danger',
        };
    }
}
