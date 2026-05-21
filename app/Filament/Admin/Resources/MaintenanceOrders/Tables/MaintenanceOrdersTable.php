<?php

declare(strict_types=1);

namespace App\Filament\Admin\Resources\MaintenanceOrders\Tables;

use App\Enums\MaintenanceOrderStatus;
use App\Enums\MaintenanceOrderType;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ForceDeleteBulkAction;
use Filament\Actions\RestoreBulkAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TrashedFilter;
use Filament\Tables\Table;

class MaintenanceOrdersTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->defaultSort('scheduled_start', 'desc')
            ->columns([
                TextColumn::make('order_number')
                    ->label(__('maintenance_orders.order_number'))
                    ->searchable()
                    ->sortable(),
                TextColumn::make('car.plate')
                    ->label(__('maintenance_orders.car'))
                    ->badge()
                    ->searchable(),
                TextColumn::make('garage.name')
                    ->label(__('maintenance_orders.garage'))
                    ->searchable()
                    ->toggleable(),
                TextColumn::make('order_type')
                    ->label(__('maintenance_orders.order_type'))
                    ->badge(),
                TextColumn::make('scheduled_start')
                    ->label(__('maintenance_orders.scheduled_start'))
                    ->dateTime()
                    ->sortable(),
                TextColumn::make('total_cost')
                    ->label(__('maintenance_orders.total_cost'))
                    ->money('EGP')
                    ->toggleable(),
                TextColumn::make('status')
                    ->label(__('maintenance_orders.status'))
                    ->badge(),
            ])
            ->filters([
                SelectFilter::make('status')->options(MaintenanceOrderStatus::class),
                SelectFilter::make('order_type')->options(MaintenanceOrderType::class),
                TrashedFilter::make(),
            ])
            ->recordActions([
                EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                    ForceDeleteBulkAction::make(),
                    RestoreBulkAction::make(),
                ]),
            ]);
    }
}
