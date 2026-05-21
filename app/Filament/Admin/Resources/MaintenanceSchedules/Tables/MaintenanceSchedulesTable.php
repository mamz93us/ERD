<?php

declare(strict_types=1);

namespace App\Filament\Admin\Resources\MaintenanceSchedules\Tables;

use App\Enums\MaintenanceServiceType;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Table;

class MaintenanceSchedulesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->defaultSort('next_due_date')
            ->columns([
                TextColumn::make('car.plate')
                    ->label(__('maintenance_schedules.car'))
                    ->badge()
                    ->searchable(),
                TextColumn::make('service_type')
                    ->label(__('maintenance_schedules.service_type'))
                    ->badge(),
                TextColumn::make('interval_km')
                    ->label(__('maintenance_schedules.interval_km'))
                    ->suffix(' km')
                    ->placeholder('—')
                    ->toggleable(),
                TextColumn::make('interval_days')
                    ->label(__('maintenance_schedules.interval_days'))
                    ->placeholder('—')
                    ->toggleable(),
                TextColumn::make('next_due_km')
                    ->label(__('maintenance_schedules.next_due_km'))
                    ->suffix(' km')
                    ->placeholder('—'),
                TextColumn::make('next_due_date')
                    ->label(__('maintenance_schedules.next_due_date'))
                    ->date()
                    ->placeholder('—')
                    ->color(fn ($state) => $state && now()->gte($state) ? 'danger' : null),
                IconColumn::make('is_active')
                    ->label(__('maintenance_schedules.is_active'))
                    ->boolean(),
            ])
            ->filters([
                SelectFilter::make('service_type')->options(MaintenanceServiceType::class),
                TernaryFilter::make('is_active'),
            ])
            ->recordActions([
                EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
