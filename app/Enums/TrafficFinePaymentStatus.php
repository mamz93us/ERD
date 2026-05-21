<?php

declare(strict_types=1);

namespace App\Enums;

use Filament\Support\Contracts\HasColor;
use Filament\Support\Contracts\HasLabel;

enum TrafficFinePaymentStatus: string implements HasColor, HasLabel
{
    case Unpaid = 'unpaid';
    case Paid = 'paid';
    case Disputed = 'disputed';
    case Waived = 'waived';

    public function getLabel(): string
    {
        return __("enums.traffic_fine_payment_status.{$this->value}");
    }

    public function getColor(): string
    {
        return match ($this) {
            self::Unpaid => 'warning',
            self::Paid => 'success',
            self::Disputed => 'danger',
            self::Waived => 'gray',
        };
    }
}
