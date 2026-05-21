<?php

declare(strict_types=1);

namespace App\Enums;

use Filament\Support\Contracts\HasColor;
use Filament\Support\Contracts\HasLabel;

enum MaintenanceOrderStatus: string implements HasColor, HasLabel
{
    case Scheduled = 'scheduled';
    case InService = 'in_service';
    case Completed = 'completed';
    case Cancelled = 'cancelled';

    public function getLabel(): string
    {
        return __("enums.maintenance_order_status.{$this->value}");
    }

    public function getColor(): string
    {
        return match ($this) {
            self::Scheduled => 'gray',
            self::InService => 'warning',
            self::Completed => 'success',
            self::Cancelled => 'danger',
        };
    }
}
