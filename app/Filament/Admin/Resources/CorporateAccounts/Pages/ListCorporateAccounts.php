<?php

namespace App\Filament\Admin\Resources\CorporateAccounts\Pages;

use App\Filament\Admin\Resources\CorporateAccounts\CorporateAccountResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListCorporateAccounts extends ListRecords
{
    protected static string $resource = CorporateAccountResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
