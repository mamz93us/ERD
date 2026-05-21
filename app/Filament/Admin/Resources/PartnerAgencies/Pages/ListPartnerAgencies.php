<?php

namespace App\Filament\Admin\Resources\PartnerAgencies\Pages;

use App\Filament\Admin\Resources\PartnerAgencies\PartnerAgencyResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListPartnerAgencies extends ListRecords
{
    protected static string $resource = PartnerAgencyResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
