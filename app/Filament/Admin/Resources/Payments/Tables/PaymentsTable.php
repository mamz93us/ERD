<?php

declare(strict_types=1);

namespace App\Filament\Admin\Resources\Payments\Tables;

use App\Enums\PaymentMethod;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Table;

class PaymentsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->defaultSort('payment_date', 'desc')
            ->columns([
                TextColumn::make('payment_number')
                    ->label(__('payments.payment_number'))
                    ->searchable()
                    ->weight('bold'),
                TextColumn::make('customer.full_name')
                    ->label(__('payments.customer'))
                    ->searchable(),
                TextColumn::make('payment_date')
                    ->label(__('payments.payment_date'))
                    ->date()
                    ->sortable(),
                TextColumn::make('method')
                    ->label(__('payments.method'))
                    ->badge(),
                TextColumn::make('amount')
                    ->label(__('payments.amount'))
                    ->money('EGP')
                    ->sortable(),
                TextColumn::make('reference_number')
                    ->label(__('payments.reference_number'))
                    ->toggleable()
                    ->placeholder('—'),
                TextColumn::make('branch.code')
                    ->label(__('payments.branch'))
                    ->badge(),
                IconColumn::make('is_reconciled')
                    ->label(__('payments.is_reconciled'))
                    ->boolean(),
            ])
            ->filters([
                SelectFilter::make('method')->options(PaymentMethod::class),
                TernaryFilter::make('is_reconciled'),
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
