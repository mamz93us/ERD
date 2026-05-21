<?php

declare(strict_types=1);

namespace App\Enums;

use Filament\Support\Contracts\HasColor;
use Filament\Support\Contracts\HasLabel;

enum CarStatus: string implements HasColor, HasLabel
{
    case Available = 'available';
    case OnTrip = 'on_trip';
    case InMaintenance = 'in_maintenance';
    case AtPartner = 'at_partner';
    case OutOfService = 'out_of_service';

    public function getLabel(): string
    {
        return __("enums.car_status.{$this->value}");
    }

    public function getColor(): string
    {
        return match ($this) {
            self::Available => 'success',
            self::OnTrip => 'primary',
            self::InMaintenance => 'warning',
            self::AtPartner => 'info',
            self::OutOfService => 'danger',
        };
    }
}
