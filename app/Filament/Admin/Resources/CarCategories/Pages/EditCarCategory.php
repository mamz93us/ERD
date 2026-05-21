<?php

namespace App\Filament\Admin\Resources\CarCategories\Pages;

use App\Filament\Admin\Resources\CarCategories\CarCategoryResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditCarCategory extends EditRecord
{
    protected static string $resource = CarCategoryResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
