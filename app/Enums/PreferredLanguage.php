<?php

declare(strict_types=1);

namespace App\Enums;

use Filament\Support\Contracts\HasLabel;

enum PreferredLanguage: string implements HasLabel
{
    case Ar = 'ar';
    case En = 'en';

    public function getLabel(): string
    {
        return match ($this) {
            self::Ar => 'العربية',
            self::En => 'English',
        };
    }
}
