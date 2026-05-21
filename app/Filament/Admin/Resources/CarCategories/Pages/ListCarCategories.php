<?php

namespace App\Filament\Admin\Resources\CarCategories\Pages;

use App\Filament\Admin\Resources\CarCategories\CarCategoryResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListCarCategories extends ListRecords
{
    protected static string $resource = CarCategoryResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
