<?php

declare(strict_types=1);

namespace App\Filament\Admin\Resources\Customers\Tables;

use App\Enums\CustomerType;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ForceDeleteBulkAction;
use Filament\Actions\RestoreBulkAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Filters\TrashedFilter;
use Filament\Tables\Table;

class CustomersTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('full_name')
                    ->label(__('customers.full_name'))
                    ->searchable()
                    ->sortable(),
                TextColumn::make('full_name_ar')
                    ->label(__('customers.full_name_ar'))
                    ->searchable()
                    ->toggleable(),
                TextColumn::make('type')
                    ->label(__('customers.type'))
                    ->badge(),
                TextColumn::make('phone')
                    ->label(__('customers.phone'))
                    ->searchable(),
                TextColumn::make('corporateAccount.company_name')
                    ->label(__('customers.corporate_account'))
                    ->placeholder('—')
                    ->toggleable(),
                TextColumn::make('loyalty_points')
                    ->label(__('customers.loyalty_points'))
                    ->sortable()
                    ->toggleable(),
                IconColumn::make('is_blacklisted')
                    ->label(__('customers.is_blacklisted'))
                    ->boolean()
                    ->trueColor('danger'),
            ])
            ->filters([
                SelectFilter::make('type')->label(__('customers.type'))->options(CustomerType::class),
                TernaryFilter::make('is_blacklisted')->label(__('customers.is_blacklisted')),
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
