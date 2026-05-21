<?php

declare(strict_types=1);

namespace App\Filament\Admin\Resources\CorporateAccounts\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ForceDeleteBulkAction;
use Filament\Actions\RestoreBulkAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Filters\TrashedFilter;
use Filament\Tables\Table;

class CorporateAccountsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('company_name')
                    ->label(__('corporate_accounts.company_name'))
                    ->searchable()
                    ->sortable(),
                TextColumn::make('company_name_ar')
                    ->label(__('corporate_accounts.company_name_ar'))
                    ->searchable()
                    ->toggleable(),
                TextColumn::make('tax_id')
                    ->label(__('corporate_accounts.tax_id'))
                    ->searchable()
                    ->toggleable(),
                TextColumn::make('industry')
                    ->label(__('corporate_accounts.industry'))
                    ->badge()
                    ->toggleable(),
                TextColumn::make('credit_limit')
                    ->label(__('corporate_accounts.credit_limit'))
                    ->money('EGP')
                    ->sortable(),
                TextColumn::make('discount_percentage')
                    ->label(__('corporate_accounts.discount_percentage'))
                    ->suffix('%')
                    ->toggleable(),
                IconColumn::make('is_active')
                    ->label(__('corporate_accounts.is_active'))
                    ->boolean(),
            ])
            ->filters([
                TernaryFilter::make('is_active')->label(__('corporate_accounts.is_active')),
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
