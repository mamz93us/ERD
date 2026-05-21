<?php

declare(strict_types=1);

namespace App\Filament\Admin\Resources\PartnerAgencies\Tables;

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

class PartnerAgenciesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->label(__('partner_agencies.name'))
                    ->searchable()
                    ->sortable(),
                TextColumn::make('name_ar')
                    ->label(__('partner_agencies.name_ar'))
                    ->searchable()
                    ->toggleable(),
                TextColumn::make('contact_person')
                    ->label(__('partner_agencies.contact_person'))
                    ->searchable()
                    ->toggleable(),
                TextColumn::make('phone')
                    ->label(__('partner_agencies.phone'))
                    ->searchable()
                    ->toggleable(),
                TextColumn::make('credit_limit')
                    ->label(__('partner_agencies.credit_limit'))
                    ->money('EGP')
                    ->sortable(),
                TextColumn::make('payment_terms_days')
                    ->label(__('partner_agencies.payment_terms_days'))
                    ->numeric()
                    ->suffix(' d')
                    ->toggleable(),
                IconColumn::make('is_active')
                    ->label(__('partner_agencies.is_active'))
                    ->boolean(),
            ])
            ->filters([
                TernaryFilter::make('is_active')->label(__('partner_agencies.is_active')),
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
