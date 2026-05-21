<?php

namespace App\Filament\Admin\Resources\InsuranceClaims\Pages;

use App\Filament\Admin\Resources\InsuranceClaims\InsuranceClaimResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditInsuranceClaim extends EditRecord
{
    protected static string $resource = InsuranceClaimResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
