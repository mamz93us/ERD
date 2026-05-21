<?php

declare(strict_types=1);

namespace App\Filament\Admin\Resources\MaintenanceSchedules;

use App\Filament\Admin\Resources\MaintenanceSchedules\Pages\CreateMaintenanceSchedule;
use App\Filament\Admin\Resources\MaintenanceSchedules\Pages\EditMaintenanceSchedule;
use App\Filament\Admin\Resources\MaintenanceSchedules\Pages\ListMaintenanceSchedules;
use App\Filament\Admin\Resources\MaintenanceSchedules\Schemas\MaintenanceScheduleForm;
use App\Filament\Admin\Resources\MaintenanceSchedules\Tables\MaintenanceSchedulesTable;
use App\Models\MaintenanceSchedule;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class MaintenanceScheduleResource extends Resource
{
    protected static ?string $model = MaintenanceSchedule::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedClock;

    public static function getNavigationLabel(): string
    {
        return __('navigation.maintenance_schedules');
    }

    public static function getNavigationGroup(): ?string
    {
        return __('navigation.maintenance');
    }

    public static function getPluralModelLabel(): string
    {
        return __('navigation.maintenance_schedules');
    }

    public static function form(Schema $schema): Schema
    {
        return MaintenanceScheduleForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return MaintenanceSchedulesTable::configure($table);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListMaintenanceSchedules::route('/'),
            'create' => CreateMaintenanceSchedule::route('/create'),
            'edit' => EditMaintenanceSchedule::route('/{record}/edit'),
        ];
    }
}
