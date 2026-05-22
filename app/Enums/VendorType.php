<?php

declare(strict_types=1);

namespace App\Enums;

use Filament\Support\Contracts\HasLabel;

enum VendorType: string implements HasLabel
{
    case PartnerAgency = 'partner_agency';
    case Garage = 'garage';
    case Fuel = 'fuel';
    case Insurance = 'insurance';
    case Other = 'other';

    public function getLabel(): string
    {
        return __("enums.vendor_type.{$this->value}");
    }
}
