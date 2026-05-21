<?php

namespace App\Filament\Admin\Resources\MaintenanceOrders\Pages;

use App\Filament\Admin\Resources\MaintenanceOrders\MaintenanceOrderResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListMaintenanceOrders extends ListRecords
{
    protected static string $resource = MaintenanceOrderResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
