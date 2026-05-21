<?php

declare(strict_types=1);

namespace App\Enums;

use Filament\Support\Contracts\HasColor;
use Filament\Support\Contracts\HasLabel;

enum CommunicationDirection: string implements HasColor, HasLabel
{
    case Inbound = 'inbound';
    case Outbound = 'outbound';

    public function getLabel(): string
    {
        return __("enums.communication_direction.{$this->value}");
    }

    public function getColor(): string
    {
        return match ($this) {
            self::Inbound => 'info',
            self::Outbound => 'primary',
        };
    }
}
