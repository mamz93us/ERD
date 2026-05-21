<?php

declare(strict_types=1);

namespace App\Filament\Admin\Resources\CarCategories\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class CarCategoriesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->defaultSort('sort_order')
            ->columns([
                TextColumn::make('sort_order')
                    ->label(__('car_categories.sort_order'))
                    ->sortable()
                    ->toggleable(),
                TextColumn::make('class_code')
                    ->label(__('car_categories.class_code'))
                    ->badge()
                    ->sortable()
                    ->searchable(),
                TextColumn::make('name')
                    ->label(__('car_categories.name'))
                    ->searchable()
                    ->sortable(),
                TextColumn::make('name_ar')
                    ->label(__('car_categories.name_ar'))
                    ->searchable(),
                TextColumn::make('default_seats')
                    ->label(__('car_categories.default_seats'))
                    ->sortable(),
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
