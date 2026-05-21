<?php

declare(strict_types=1);

namespace App\Filament\Admin\Resources\RateCards\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Table;

class RateCardsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->defaultSort('category_id')
            ->columns([
                TextColumn::make('category.name')
                    ->label(__('rate_cards.category'))
                    ->sortable()
                    ->searchable(),
                TextColumn::make('name')
                    ->label(__('rate_cards.name'))
                    ->searchable(),
                TextColumn::make('corporateAccount.company_name')
                    ->label(__('rate_cards.corporate_account'))
                    ->placeholder(__('rate_cards.default'))
                    ->toggleable(),
                TextColumn::make('hourly_rate')->label(__('rate_cards.hourly_rate'))->money('EGP')->toggleable(),
                TextColumn::make('daily_rate')->label(__('rate_cards.daily_rate'))->money('EGP'),
                TextColumn::make('weekly_rate')->label(__('rate_cards.weekly_rate'))->money('EGP')->toggleable(),
                TextColumn::make('monthly_rate')->label(__('rate_cards.monthly_rate'))->money('EGP')->toggleable(),
                TextColumn::make('included_km_per_day')->label(__('rate_cards.included_km_per_day'))->suffix(' km')->toggleable(),
                TextColumn::make('effective_from')->label(__('rate_cards.effective_from'))->date()->toggleable(),
                TextColumn::make('effective_to')->label(__('rate_cards.effective_to'))->date()->placeholder('—')->toggleable(),
                IconColumn::make('is_active')->label(__('rate_cards.is_active'))->boolean(),
            ])
            ->filters([
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
