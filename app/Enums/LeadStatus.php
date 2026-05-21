<?php

declare(strict_types=1);

namespace App\Enums;

use Filament\Support\Contracts\HasColor;
use Filament\Support\Contracts\HasLabel;

enum LeadStatus: string implements HasColor, HasLabel
{
    case New_ = 'new';
    case Contacted = 'contacted';
    case Quoted = 'quoted';
    case Won = 'won';
    case Lost = 'lost';

    public function getLabel(): string
    {
        return __("enums.lead_status.{$this->value}");
    }

    public function getColor(): string
    {
        return match ($this) {
            self::New_ => 'gray',
            self::Contacted => 'info',
            self::Quoted => 'warning',
            self::Won => 'success',
            self::Lost => 'danger',
        };
    }
}
