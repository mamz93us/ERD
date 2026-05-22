<?php

declare(strict_types=1);

namespace App\Filament\Admin\Resources\Expenses\Tables;

use App\Enums\ExpenseCategory;
use App\Enums\ExpensePaidBy;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class ExpensesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->defaultSort('expense_date', 'desc')
            ->columns([
                TextColumn::make('expense_date')
                    ->label(__('expenses.expense_date'))
                    ->date()
                    ->sortable(),
                TextColumn::make('branch.code')
                    ->label(__('expenses.branch'))
                    ->badge(),
                TextColumn::make('category')
                    ->label(__('expenses.category'))
                    ->badge(),
                TextColumn::make('amount')
                    ->label(__('expenses.amount'))
                    ->money('EGP')
                    ->sortable(),
                TextColumn::make('car.plate')
                    ->label(__('expenses.car'))
                    ->placeholder('—')
                    ->toggleable(),
                TextColumn::make('driver.full_name')
                    ->label(__('expenses.driver'))
                    ->placeholder('—')
                    ->toggleable(),
                TextColumn::make('paid_by')
                    ->label(__('expenses.paid_by'))
                    ->badge(),
                TextColumn::make('paidByUser.full_name')
                    ->label(__('expenses.paid_by_user'))
                    ->toggleable(),
                TextColumn::make('description')
                    ->label(__('expenses.description'))
                    ->limit(40)
                    ->toggleable(),
            ])
            ->filters([
                SelectFilter::make('category')->options(ExpenseCategory::class),
                SelectFilter::make('paid_by')->options(ExpensePaidBy::class),
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
