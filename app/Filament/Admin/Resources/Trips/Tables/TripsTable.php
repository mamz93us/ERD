<?php

declare(strict_types=1);

namespace App\Filament\Admin\Resources\Trips\Tables;

use App\Enums\TripStatus;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ForceDeleteBulkAction;
use Filament\Actions\RestoreBulkAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TrashedFilter;
use Filament\Tables\Table;

class TripsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->defaultSort('scheduled_start', 'desc')
            ->columns([
                TextColumn::make('trip_number')
                    ->label(__('trips.trip_number'))
                    ->searchable()
                    ->sortable(),
                TextColumn::make('branch.code')
                    ->label(__('trips.branch'))
                    ->badge(),
                TextColumn::make('customer.full_name')
                    ->label(__('trips.customer'))
                    ->searchable()
                    ->toggleable(),
                TextColumn::make('car.plate')
                    ->label(__('trips.car'))
                    ->badge(),
                TextColumn::make('driver.full_name')
                    ->label(__('trips.driver'))
                    ->toggleable(),
                TextColumn::make('scheduled_start')
                    ->label(__('trips.scheduled_start'))
                    ->dateTime()
                    ->sortable(),
                TextColumn::make('scheduled_end')
                    ->label(__('trips.scheduled_end'))
                    ->dateTime()
                    ->toggleable(),
                TextColumn::make('total_amount')
                    ->label(__('trips.total_amount'))
                    ->money('EGP')
                    ->toggleable(),
                TextColumn::make('status')
                    ->label(__('trips.status'))
                    ->badge(),
            ])
            ->filters([
                SelectFilter::make('status')->options(TripStatus::class),
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
