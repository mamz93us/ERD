<?php

declare(strict_types=1);

namespace App\Enums;

use Filament\Support\Contracts\HasLabel;

enum CarDocumentType: string implements HasLabel
{
    case RegistrationLicense = 'registration_license';
    case CompulsoryInsurance = 'compulsory_insurance';
    case ComprehensiveInsurance = 'comprehensive_insurance';
    case TechnicalInspection = 'technical_inspection';
    case InspectionSticker = 'inspection_sticker';

    public function getLabel(): string
    {
        return __("enums.car_document_type.{$this->value}");
    }
}
