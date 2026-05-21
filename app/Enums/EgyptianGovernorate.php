<?php

declare(strict_types=1);

namespace App\Enums;

use Filament\Support\Contracts\HasLabel;

/**
 * Egypt's 27 administrative governorates.
 *
 * Used for trip pickup/dropoff locations + auto cross-city surcharge detection
 * in PricingService (when pickup_location != dropoff_location, the surcharge
 * is auto-applied).
 */
enum EgyptianGovernorate: string implements HasLabel
{
    case Alexandria = 'alexandria';
    case Aswan = 'aswan';
    case Asyut = 'asyut';
    case Beheira = 'beheira';
    case BeniSuef = 'beni_suef';
    case Cairo = 'cairo';
    case Dakahlia = 'dakahlia';
    case Damietta = 'damietta';
    case Faiyum = 'faiyum';
    case Gharbia = 'gharbia';
    case Giza = 'giza';
    case Ismailia = 'ismailia';
    case KafrElSheikh = 'kafr_el_sheikh';
    case Luxor = 'luxor';
    case Matrouh = 'matrouh';
    case Minya = 'minya';
    case Monufia = 'monufia';
    case NewValley = 'new_valley';
    case NorthSinai = 'north_sinai';
    case PortSaid = 'port_said';
    case Qalyubia = 'qalyubia';
    case Qena = 'qena';
    case RedSea = 'red_sea';
    case Sharqia = 'sharqia';
    case Sohag = 'sohag';
    case SouthSinai = 'south_sinai';
    case Suez = 'suez';

    public function getLabel(): string
    {
        return __("enums.egyptian_governorate.{$this->value}");
    }
}
