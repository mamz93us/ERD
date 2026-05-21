<?php

declare(strict_types=1);

namespace App\Enums;

use Filament\Support\Contracts\HasLabel;

enum CarTransmission: string implements HasLabel
{
    case Manual = 'manual';
    case Auto = 'auto';

    public function getLabel(): string
    {
        return __("enums.car_transmission.{$this->value}");
    }
}
