<?php

namespace App\Filament\Admin\Resources\MaintenanceSchedules\Pages;

use App\Filament\Admin\Resources\MaintenanceSchedules\MaintenanceScheduleResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditMaintenanceSchedule extends EditRecord
{
    protected static string $resource = MaintenanceScheduleResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
