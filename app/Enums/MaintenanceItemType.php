<?php

declare(strict_types=1);

namespace App\Enums;

use Filament\Support\Contracts\HasLabel;

enum MaintenanceItemType: string implements HasLabel
{
    case Part = 'part';
    case Labor = 'labor';
    case Consumable = 'consumable';

    public function getLabel(): string
    {
        return __("enums.maintenance_item_type.{$this->value}");
    }
}
