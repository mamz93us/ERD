<?php

declare(strict_types=1);

namespace App\Filament\Admin\Resources\MaintenanceSchedules\Schemas;

use App\Enums\MaintenanceServiceType;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;

class MaintenanceScheduleForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema->components([
            Select::make('car_id')
                ->label(__('maintenance_schedules.car'))
                ->relationship('car', 'plate')
                ->searchable()
                ->preload()
                ->required(),
            Select::make('service_type')
                ->label(__('maintenance_schedules.service_type'))
                ->options(MaintenanceServiceType::class)
                ->required(),
            TextInput::make('interval_km')
                ->label(__('maintenance_schedules.interval_km'))
                ->numeric()
                ->suffix(' km')
                ->helperText(__('maintenance_schedules.interval_km_help')),
            TextInput::make('interval_days')
                ->label(__('maintenance_schedules.interval_days'))
                ->numeric()
                ->suffix(__('maintenance_schedules.days_suffix'))
                ->helperText(__('maintenance_schedules.interval_days_help')),
            TextInput::make('last_done_km')
                ->label(__('maintenance_schedules.last_done_km'))
                ->numeric()
                ->suffix(' km')
                ->helperText(__('maintenance_schedules.last_done_help')),
            TextInput::make('last_done_date')
                ->label(__('maintenance_schedules.last_done_date'))
                ->type('date'),
            Toggle::make('is_active')
                ->label(__('maintenance_schedules.is_active'))
                ->default(true),
        ]);
    }
}
