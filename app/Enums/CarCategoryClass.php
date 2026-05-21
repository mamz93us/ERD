<?php

declare(strict_types=1);

namespace App\Enums;

use Filament\Support\Contracts\HasLabel;

enum CarCategoryClass: string implements HasLabel
{
    case Economy = 'economy';
    case Midsize = 'midsize';
    case Suv = 'suv';
    case Luxury = 'luxury';
    case Van = 'van';
    case Minibus = 'minibus';

    public function getLabel(): string
    {
        return __("enums.car_category_class.{$this->value}");
    }
}
