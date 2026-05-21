<?php

namespace App\Filament\Admin\Resources\SubRentalContracts\Pages;

use App\Filament\Admin\Resources\SubRentalContracts\SubRentalContractResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListSubRentalContracts extends ListRecords
{
    protected static string $resource = SubRentalContractResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
