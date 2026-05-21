<?php

declare(strict_types=1);

namespace App\Enums;

use Filament\Support\Contracts\HasColor;
use Filament\Support\Contracts\HasLabel;

enum TripDamageReportStatus: string implements HasColor, HasLabel
{
    case Reported = 'reported';
    case Approved = 'approved';
    case Repaired = 'repaired';
    case Disputed = 'disputed';

    public function getLabel(): string
    {
        return __("enums.trip_damage_report_status.{$this->value}");
    }

    public function getColor(): string
    {
        return match ($this) {
            self::Reported => 'warning',
            self::Approved => 'info',
            self::Repaired => 'success',
            self::Disputed => 'danger',
        };
    }
}
