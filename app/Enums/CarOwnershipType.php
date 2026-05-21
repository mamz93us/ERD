<?php

declare(strict_types=1);

namespace App\Enums;

use Filament\Support\Contracts\HasColor;
use Filament\Support\Contracts\HasLabel;

enum CarOwnershipType: string implements HasColor, HasLabel
{
    case Owned = 'owned';
    case SubRented = 'sub_rented';
    case Replacement = 'replacement';

    public function getLabel(): string
    {
        return __("enums.car_ownership_type.{$this->value}");
    }

    public function getColor(): string
    {
        return match ($this) {
            self::Owned => 'success',
            self::SubRented => 'warning',
            self::Replacement => 'info',
        };
    }
}
