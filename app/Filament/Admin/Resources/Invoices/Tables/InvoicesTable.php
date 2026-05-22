<?php

declare(strict_types=1);

namespace App\Filament\Admin\Resources\Invoices\Tables;

use App\Enums\InvoiceStatus;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class InvoicesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->defaultSort('issue_date', 'desc')
            ->columns([
                TextColumn::make('invoice_number')
                    ->label(__('invoices.invoice_number'))
                    ->searchable()
                    ->sortable()
                    ->weight('bold'),
                TextColumn::make('customer.full_name')
                    ->label(__('invoices.customer'))
                    ->searchable()
                    ->toggleable(),
                TextColumn::make('corporateAccount.company_name')
                    ->label(__('invoices.corporate_account'))
                    ->placeholder('—')
                    ->toggleable(),
                TextColumn::make('issue_date')
                    ->label(__('invoices.issue_date'))
                    ->date()
                    ->sortable(),
                TextColumn::make('due_date')
                    ->label(__('invoices.due_date'))
                    ->date()
                    ->sortable(),
                TextColumn::make('total')
                    ->label(__('invoices.total'))
                    ->money('EGP')
                    ->sortable(),
                TextColumn::make('paid_amount')
                    ->label(__('invoices.paid_amount'))
                    ->money('EGP')
                    ->toggleable(),
                TextColumn::make('balance_due')
                    ->label(__('invoices.balance_due'))
                    ->money('EGP')
                    ->sortable(),
                TextColumn::make('status')
                    ->label(__('invoices.status'))
                    ->badge(),
            ])
            ->filters([
                SelectFilter::make('status')->options(InvoiceStatus::class),
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
