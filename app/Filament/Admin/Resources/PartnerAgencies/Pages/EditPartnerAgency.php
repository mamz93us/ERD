<?php

namespace App\Filament\Admin\Resources\PartnerAgencies\Pages;

use App\Filament\Admin\Resources\PartnerAgencies\PartnerAgencyResource;
use Filament\Actions\DeleteAction;
use Filament\Actions\ForceDeleteAction;
use Filament\Actions\RestoreAction;
use Filament\Resources\Pages\EditRecord;

class EditPartnerAgency extends EditRecord
{
    protected static string $resource = PartnerAgencyResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
            ForceDeleteAction::make(),
            RestoreAction::make(),
        ];
    }
}
