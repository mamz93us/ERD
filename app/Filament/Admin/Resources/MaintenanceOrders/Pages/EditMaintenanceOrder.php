<?php

namespace App\Filament\Admin\Resources\MaintenanceOrders\Pages;

use App\Filament\Admin\Resources\MaintenanceOrders\MaintenanceOrderResource;
use Filament\Actions\DeleteAction;
use Filament\Actions\ForceDeleteAction;
use Filament\Actions\RestoreAction;
use Filament\Resources\Pages\EditRecord;

class EditMaintenanceOrder extends EditRecord
{
    protected static string $resource = MaintenanceOrderResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
            ForceDeleteAction::make(),
            RestoreAction::make(),
        ];
    }
}
