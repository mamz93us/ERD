<?php

declare(strict_types=1);

namespace App\Enums;

use Filament\Support\Contracts\HasLabel;

enum DriverDocumentType: string implements HasLabel
{
    case DrivingLicense = 'driving_license';
    case NationalId = 'national_id';
    case CriminalRecord = 'criminal_record';
    case MedicalCertificate = 'medical_certificate';
    case ProfessionalLicense = 'professional_license';

    public function getLabel(): string
    {
        return __("enums.driver_document_type.{$this->value}");
    }
}
