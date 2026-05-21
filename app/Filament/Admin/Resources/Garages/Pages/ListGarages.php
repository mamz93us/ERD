<?php

namespace App\Filament\Admin\Resources\Garages\Pages;

use App\Filament\Admin\Resources\Garages\GarageResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListGarages extends ListRecords
{
    protected static string $resource = GarageResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
