<?php

declare(strict_types=1);

namespace App\Filament\Admin\Resources\TrafficFines\Tables;

use App\Enums\TrafficFinePaymentStatus;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Table;

class TrafficFinesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->defaultSort('violation_date', 'desc')
            ->columns([
                TextColumn::make('violation_number')
                    ->label(__('traffic_fines.violation_number'))
                    ->searchable()
                    ->sortable(),
                TextColumn::make('car.plate')
                    ->label(__('traffic_fines.car'))
                    ->badge()
                    ->searchable(),
                TextColumn::make('trip.trip_number')
                    ->label(__('traffic_fines.trip'))
                    ->placeholder(__('traffic_fines.unattributed'))
                    ->badge()
                    ->color('info'),
                TextColumn::make('driver.full_name')
                    ->label(__('traffic_fines.driver'))
                    ->placeholder('—')
                    ->toggleable(),
                TextColumn::make('violation_date')
                    ->label(__('traffic_fines.violation_date'))
                    ->dateTime()
                    ->sortable(),
                TextColumn::make('violation_type')
                    ->label(__('traffic_fines.violation_type'))
                    ->toggleable(),
                TextColumn::make('amount')
                    ->label(__('traffic_fines.amount'))
                    ->money('EGP'),
                TextColumn::make('payment_status')
                    ->label(__('traffic_fines.payment_status'))
                    ->badge(),
                IconColumn::make('deducted_from_driver')
                    ->label(__('traffic_fines.deducted_from_driver'))
                    ->boolean(),
            ])
            ->filters([
                SelectFilter::make('payment_status')->options(TrafficFinePaymentStatus::class),
                TernaryFilter::make('deducted_from_driver'),
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
