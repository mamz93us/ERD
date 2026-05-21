<?php

declare(strict_types=1);

namespace App\Enums;

use Filament\Support\Contracts\HasLabel;

enum FuelLevel: string implements HasLabel
{
    case Empty = 'empty';
    case Quarter = 'quarter';
    case Half = 'half';
    case ThreeQuarter = 'three_quarter';
    case Full = 'full';

    public function getLabel(): string
    {
        return __("enums.fuel_level.{$this->value}");
    }

    /** Numeric value 0–4 for diff math (e.g. fuel delta between pickup and return). */
    public function level(): int
    {
        return match ($this) {
            self::Empty => 0,
            self::Quarter => 1,
            self::Half => 2,
            self::ThreeQuarter => 3,
            self::Full => 4,
        };
    }
}
