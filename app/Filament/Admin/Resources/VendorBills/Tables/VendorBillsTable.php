<?php

declare(strict_types=1);

namespace App\Filament\Admin\Resources\VendorBills\Tables;

use App\Enums\VendorBillStatus;
use App\Enums\VendorType;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class VendorBillsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->defaultSort('bill_date', 'desc')
            ->columns([
                TextColumn::make('bill_number')
                    ->label(__('vendor_bills.bill_number'))
                    ->searchable()
                    ->weight('bold'),
                TextColumn::make('vendor_type')
                    ->label(__('vendor_bills.vendor_type'))
                    ->badge(),
                TextColumn::make('partnerAgency.name')
                    ->label(__('vendor_bills.partner_agency'))
                    ->placeholder('—')
                    ->toggleable(),
                TextColumn::make('garage.name')
                    ->label(__('vendor_bills.garage'))
                    ->placeholder('—')
                    ->toggleable(),
                TextColumn::make('bill_date')
                    ->label(__('vendor_bills.bill_date'))
                    ->date()
                    ->sortable(),
                TextColumn::make('due_date')
                    ->label(__('vendor_bills.due_date'))
                    ->date()
                    ->placeholder('—')
                    ->sortable(),
                TextColumn::make('total')
                    ->label(__('vendor_bills.total'))
                    ->money('EGP'),
                TextColumn::make('balance_due')
                    ->label(__('vendor_bills.balance_due'))
                    ->money('EGP'),
                TextColumn::make('status')
                    ->label(__('vendor_bills.status'))
                    ->badge(),
            ])
            ->filters([
                SelectFilter::make('vendor_type')->options(VendorType::class),
                SelectFilter::make('status')->options(VendorBillStatus::class),
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
