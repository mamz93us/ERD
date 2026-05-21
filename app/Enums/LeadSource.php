<?php

declare(strict_types=1);

namespace App\Enums;

use Filament\Support\Contracts\HasLabel;

enum LeadSource: string implements HasLabel
{
    case Whatsapp = 'whatsapp';
    case Website = 'website';
    case Referral = 'referral';
    case WalkIn = 'walk_in';
    case Phone = 'phone';
    case Corporate = 'corporate';

    public function getLabel(): string
    {
        return __("enums.lead_source.{$this->value}");
    }
}
