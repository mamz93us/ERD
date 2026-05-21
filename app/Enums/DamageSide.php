<?php

declare(strict_types=1);

namespace App\Enums;

use Filament\Support\Contracts\HasLabel;

enum DamageSide: string implements HasLabel
{
    case FrontLeft = 'front_left';
    case FrontRight = 'front_right';
    case RearLeft = 'rear_left';
    case RearRight = 'rear_right';
    case Top = 'top';
    case Bottom = 'bottom';

    public function getLabel(): string
    {
        return __("enums.damage_side.{$this->value}");
    }
}
