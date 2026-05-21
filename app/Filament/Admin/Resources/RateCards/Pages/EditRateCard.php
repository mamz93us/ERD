<?php

namespace App\Filament\Admin\Resources\RateCards\Pages;

use App\Filament\Admin\Resources\RateCards\RateCardResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditRateCard extends EditRecord
{
    protected static string $resource = RateCardResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
