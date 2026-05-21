<?php

declare(strict_types=1);

namespace App\Filament\Admin\Resources\SubRentalContracts\Tables;

use App\Enums\SubRentalContractStatus;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class SubRentalContractsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->defaultSort('end_date', 'desc')
            ->columns([
                TextColumn::make('car.plate')
                    ->label(__('sub_rental_contracts.car'))
                    ->searchable()
                    ->sortable(),
                TextColumn::make('partnerAgency.name')
                    ->label(__('sub_rental_contracts.partner_agency'))
                    ->searchable(),
                TextColumn::make('start_date')
                    ->label(__('sub_rental_contracts.start_date'))
                    ->date()
                    ->sortable(),
                TextColumn::make('end_date')
                    ->label(__('sub_rental_contracts.end_date'))
                    ->date()
                    ->sortable()
                    ->color(fn ($state) => $state && now()->gt($state) ? 'danger' : null),
                TextColumn::make('daily_cost')
                    ->label(__('sub_rental_contracts.daily_cost'))
                    ->money('EGP'),
                TextColumn::make('status')
                    ->label(__('sub_rental_contracts.status'))
                    ->badge(),
            ])
            ->filters([
                SelectFilter::make('status')->options(SubRentalContractStatus::class),
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
