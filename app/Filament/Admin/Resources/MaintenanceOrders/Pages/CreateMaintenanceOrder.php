<?php

namespace App\Filament\Admin\Resources\MaintenanceOrders\Pages;

use App\Filament\Admin\Resources\MaintenanceOrders\MaintenanceOrderResource;
use Filament\Resources\Pages\CreateRecord;

class CreateMaintenanceOrder extends CreateRecord
{
    protected static string $resource = MaintenanceOrderResource::class;
}
