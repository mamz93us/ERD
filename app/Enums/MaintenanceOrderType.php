<?php

declare(strict_types=1);

namespace App\Enums;

use Filament\Support\Contracts\HasColor;
use Filament\Support\Contracts\HasLabel;

enum MaintenanceOrderType: string implements HasColor, HasLabel
{
    case Preventive = 'preventive';
    case Corrective = 'corrective';
    case AccidentRepair = 'accident_repair';

    public function getLabel(): string
    {
        return __("enums.maintenance_order_type.{$this->value}");
    }

    public function getColor(): string
    {
        return match ($this) {
            self::Preventive => 'info',
            self::Corrective => 'warning',
            self::AccidentRepair => 'danger',
        };
    }
}
