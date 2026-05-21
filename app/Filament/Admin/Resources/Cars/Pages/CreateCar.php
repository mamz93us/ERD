<?php

namespace App\Filament\Admin\Resources\Cars\Pages;

use App\Filament\Admin\Resources\Cars\CarResource;
use Filament\Resources\Pages\CreateRecord;

class CreateCar extends CreateRecord
{
    protected static string $resource = CarResource::class;
}
