<?php

declare(strict_types=1);

namespace App\Filament\Admin\Resources\Trips\RelationManagers;

use App\Enums\TripExpenseType;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\CreateAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Table;

class ExpensesRelationManager extends RelationManager
{
    protected static string $relationship = 'expenses';

    public function form(Schema $schema): Schema
    {
        return $schema->components([
            Select::make('type')
                ->label(__('trip_expenses.type'))
                ->options(TripExpenseType::class)
                ->required(),
            TextInput::make('amount')
                ->label(__('trip_expenses.amount'))
                ->numeric()
                ->prefix('EGP')
                ->required(),
            DateTimePicker::make('incurred_at')
                ->label(__('trip_expenses.incurred_at'))
                ->default(now())
                ->required()
                ->seconds(false),
            FileUpload::make('receipt_path')
                ->label(__('trip_expenses.receipt'))
                ->disk('public')
                ->directory('expense-receipts'),
            Toggle::make('reimbursed')
                ->label(__('trip_expenses.reimbursed'))
                ->default(false),
            Textarea::make('notes')
                ->label(__('trip_expenses.notes'))
                ->rows(2)
                ->columnSpanFull(),
        ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('type')
            ->defaultSort('incurred_at', 'desc')
            ->columns([
                TextColumn::make('type')
                    ->label(__('trip_expenses.type'))
                    ->badge(),
                TextColumn::make('amount')
                    ->label(__('trip_expenses.amount'))
                    ->money('EGP')
                    ->sortable(),
                TextColumn::make('incurred_at')
                    ->label(__('trip_expenses.incurred_at'))
                    ->dateTime(),
                IconColumn::make('reimbursed')
                    ->label(__('trip_expenses.reimbursed'))
                    ->boolean(),
                TextColumn::make('notes')
                    ->label(__('trip_expenses.notes'))
                    ->limit(40)
                    ->toggleable(),
            ])
            ->filters([
                SelectFilter::make('type')->options(TripExpenseType::class),
                TernaryFilter::make('reimbursed'),
            ])
            ->headerActions([
                CreateAction::make(),
            ])
            ->recordActions([
                EditAction::make(),
                DeleteAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
