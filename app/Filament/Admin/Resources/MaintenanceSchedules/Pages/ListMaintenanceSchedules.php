<?php

namespace App\Filament\Admin\Resources\MaintenanceSchedules\Pages;

use App\Filament\Admin\Resources\MaintenanceSchedules\MaintenanceScheduleResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListMaintenanceSchedules extends ListRecords
{
    protected static string $resource = MaintenanceScheduleResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
