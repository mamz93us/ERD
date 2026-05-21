<?php

namespace App\Filament\Admin\Resources\RateCards\Pages;

use App\Filament\Admin\Resources\RateCards\RateCardResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListRateCards extends ListRecords
{
    protected static string $resource = RateCardResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
