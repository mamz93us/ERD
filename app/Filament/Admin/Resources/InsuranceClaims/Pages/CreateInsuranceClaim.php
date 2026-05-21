<?php

namespace App\Filament\Admin\Resources\InsuranceClaims\Pages;

use App\Filament\Admin\Resources\InsuranceClaims\InsuranceClaimResource;
use Filament\Resources\Pages\CreateRecord;

class CreateInsuranceClaim extends CreateRecord
{
    protected static string $resource = InsuranceClaimResource::class;
}
