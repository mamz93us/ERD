<?php

declare(strict_types=1);

namespace App\Enums;

use Filament\Support\Contracts\HasLabel;

enum CustomerType: string implements HasLabel
{
    case Individual = 'individual';
    case CorporateContact = 'corporate_contact';
    case Vip = 'vip';

    public function getLabel(): string
    {
        return __("enums.customer_type.{$this->value}");
    }
}
