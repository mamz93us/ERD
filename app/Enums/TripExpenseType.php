<?php

declare(strict_types=1);

namespace App\Enums;

use Filament\Support\Contracts\HasLabel;

enum TripExpenseType: string implements HasLabel
{
    case Fuel = 'fuel';
    case Toll = 'toll';
    case Parking = 'parking';
    case Food = 'food';
    case Accommodation = 'accommodation';
    case Misc = 'misc';

    public function getLabel(): string
    {
        return __("enums.trip_expense_type.{$this->value}");
    }
}
