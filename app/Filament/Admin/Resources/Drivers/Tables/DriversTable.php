<?php

declare(strict_types=1);

namespace App\Filament\Admin\Resources\Drivers\Tables;

use App\Enums\DriverStatus;
use App\Enums\EmploymentType;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ForceDeleteBulkAction;
use Filament\Actions\RestoreBulkAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TrashedFilter;
use Filament\Tables\Table;

class DriversTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('full_name')
                    ->label(__('drivers.full_name'))
                    ->searchable()
                    ->sortable(),
                TextColumn::make('full_name_ar')
                    ->label(__('drivers.full_name_ar'))
                    ->searchable()
                    ->toggleable(),
                TextColumn::make('branch.code')
                    ->label(__('drivers.branch'))
                    ->badge()
                    ->sortable(),
                TextColumn::make('phone')
                    ->label(__('drivers.phone'))
                    ->searchable(),
                TextColumn::make('national_id')
                    ->label(__('drivers.national_id'))
                    ->searchable()
                    ->toggleable(),
                TextColumn::make('employment_type')
                    ->label(__('drivers.employment_type'))
                    ->badge(),
                TextColumn::make('status')
                    ->label(__('drivers.status'))
                    ->badge(),
                TextColumn::make('rating')
                    ->label(__('drivers.rating'))
                    ->numeric(2)
                    ->sortable()
                    ->toggleable(),
            ])
            ->filters([
                SelectFilter::make('status')->options(DriverStatus::class),
                SelectFilter::make('employment_type')->options(EmploymentType::class),
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
