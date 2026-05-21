<?php

declare(strict_types=1);

namespace App\Filament\Admin\Resources\Cars\Tables;

use App\Enums\CarFuelType;
use App\Enums\CarOwnershipType;
use App\Enums\CarStatus;
use App\Enums\CarTransmission;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ForceDeleteBulkAction;
use Filament\Actions\RestoreBulkAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TrashedFilter;
use Filament\Tables\Table;

class CarsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('plate')
                    ->label(__('cars.plate'))
                    ->searchable()
                    ->sortable(),
                TextColumn::make('branch.code')
                    ->label(__('cars.branch'))
                    ->badge(),
                TextColumn::make('category.name')
                    ->label(__('cars.category'))
                    ->sortable(),
                TextColumn::make('make')
                    ->label(__('cars.make'))
                    ->searchable(),
                TextColumn::make('model')
                    ->label(__('cars.model'))
                    ->searchable(),
                TextColumn::make('year')
                    ->label(__('cars.year'))
                    ->sortable(),
                TextColumn::make('status')
                    ->label(__('cars.status'))
                    ->badge(),
                TextColumn::make('ownership_type')
                    ->label(__('cars.ownership_type'))
                    ->badge(),
                TextColumn::make('current_odometer')
                    ->label(__('cars.current_odometer'))
                    ->numeric()
                    ->suffix(' km')
                    ->sortable()
                    ->toggleable(),
            ])
            ->filters([
                SelectFilter::make('status')->options(CarStatus::class),
                SelectFilter::make('ownership_type')->options(CarOwnershipType::class),
                SelectFilter::make('transmission')->options(CarTransmission::class),
                SelectFilter::make('fuel_type')->options(CarFuelType::class),
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
