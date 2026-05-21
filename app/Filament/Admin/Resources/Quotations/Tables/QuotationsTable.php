<?php

declare(strict_types=1);

namespace App\Filament\Admin\Resources\Quotations\Tables;

use App\Enums\QuotationStatus;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ForceDeleteBulkAction;
use Filament\Actions\RestoreBulkAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TrashedFilter;
use Filament\Tables\Table;

class QuotationsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->defaultSort('created_at', 'desc')
            ->columns([
                TextColumn::make('quotation_number')
                    ->label(__('quotations.quotation_number'))
                    ->searchable()
                    ->sortable(),
                TextColumn::make('customer.full_name')
                    ->label(__('quotations.customer'))
                    ->searchable(),
                TextColumn::make('category.name')
                    ->label(__('quotations.category'))
                    ->badge()
                    ->toggleable(),
                TextColumn::make('pickup_at')
                    ->label(__('quotations.pickup_at'))
                    ->dateTime()
                    ->sortable(),
                TextColumn::make('total_amount')
                    ->label(__('quotations.total_amount'))
                    ->money('EGP')
                    ->sortable(),
                TextColumn::make('valid_until')
                    ->label(__('quotations.valid_until'))
                    ->date()
                    ->color(fn ($state) => $state && now()->gt($state) ? 'danger' : null),
                TextColumn::make('status')
                    ->label(__('quotations.status'))
                    ->badge(),
            ])
            ->filters([
                SelectFilter::make('status')->options(QuotationStatus::class),
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
