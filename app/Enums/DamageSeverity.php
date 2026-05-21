<?php

declare(strict_types=1);

namespace App\Enums;

use Filament\Support\Contracts\HasColor;
use Filament\Support\Contracts\HasLabel;

/**
 * Used inside the damage_marks JSON on trip_inspections and trip_damage_reports
 * per spec §9.1. Not persisted as a column, but enums make the form/seed/test
 * consistent.
 */
enum DamageSeverity: string implements HasColor, HasLabel
{
    case Scratch = 'scratch';
    case Dent = 'dent';
    case Crack = 'crack';
    case Missing = 'missing';
    case Other = 'other';

    public function getLabel(): string
    {
        return __("enums.damage_severity.{$this->value}");
    }

    public function getColor(): string
    {
        return match ($this) {
            self::Scratch => 'warning',
            self::Dent, self::Crack => 'danger',
            self::Missing => 'gray',
            self::Other => 'info',
        };
    }
}
