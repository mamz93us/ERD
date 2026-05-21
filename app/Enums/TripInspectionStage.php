<?php

declare(strict_types=1);

namespace App\Enums;

use Filament\Support\Contracts\HasColor;
use Filament\Support\Contracts\HasLabel;

enum TripInspectionStage: string implements HasColor, HasLabel
{
    case Pickup = 'pickup';
    case Return_ = 'return';

    public function getLabel(): string
    {
        return __("enums.trip_inspection_stage.{$this->value}");
    }

    public function getColor(): string
    {
        return match ($this) {
            self::Pickup => 'info',
            self::Return_ => 'primary',
        };
    }
}
