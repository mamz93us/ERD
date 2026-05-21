<?php

namespace App\Filament\Admin\Resources\SubRentalContracts\Pages;

use App\Filament\Admin\Resources\SubRentalContracts\SubRentalContractResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditSubRentalContract extends EditRecord
{
    protected static string $resource = SubRentalContractResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
