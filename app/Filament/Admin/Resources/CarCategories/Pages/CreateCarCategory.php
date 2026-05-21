<?php

namespace App\Filament\Admin\Resources\CarCategories\Pages;

use App\Filament\Admin\Resources\CarCategories\CarCategoryResource;
use Filament\Resources\Pages\CreateRecord;

class CreateCarCategory extends CreateRecord
{
    protected static string $resource = CarCategoryResource::class;
}
