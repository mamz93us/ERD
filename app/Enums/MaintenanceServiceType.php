<?php

declare(strict_types=1);

namespace App\Enums;

use Filament\Support\Contracts\HasLabel;

enum MaintenanceServiceType: string implements HasLabel
{
    case OilChange = 'oil_change';
    case TireRotation = 'tire_rotation';
    case BrakeInspection = 'brake_inspection';
    case MajorService = 'major_service';
    case AcService = 'ac_service';
    case BatteryCheck = 'battery_check';
    case Other = 'other';

    public function getLabel(): string
    {
        return __("enums.maintenance_service_type.{$this->value}");
    }
}
