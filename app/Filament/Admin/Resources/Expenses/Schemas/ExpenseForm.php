<?php

declare(strict_types=1);

namespace App\Filament\Admin\Resources\Expenses\Schemas;

use App\Enums\ExpenseCategory;
use App\Enums\ExpensePaidBy;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class ExpenseForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema->components([
            Select::make('branch_id')
                ->label(__('expenses.branch'))
                ->relationship('branch', 'code')
                ->required(),
            Select::make('category')
                ->label(__('expenses.category'))
                ->options(ExpenseCategory::class)
                ->required(),
            Select::make('car_id')
                ->label(__('expenses.car'))
                ->relationship('car', 'plate')
                ->searchable()
                ->preload()
                ->nullable(),
            Select::make('driver_id')
                ->label(__('expenses.driver'))
                ->relationship('driver', 'full_name')
                ->searchable()
                ->preload()
                ->nullable(),
            TextInput::make('amount')
                ->label(__('expenses.amount'))
                ->numeric()
                ->prefix('EGP')
                ->required(),
            DatePicker::make('expense_date')
                ->label(__('expenses.expense_date'))
                ->required()
                ->default(now()),
            Select::make('paid_by')
                ->label(__('expenses.paid_by'))
                ->options(ExpensePaidBy::class)
                ->required(),
            Select::make('paid_by_user_id')
                ->label(__('expenses.paid_by_user'))
                ->relationship('paidByUser', 'full_name')
                ->searchable()
                ->preload()
                ->default(auth()->id())
                ->required(),
            Textarea::make('description')
                ->label(__('expenses.description'))
                ->columnSpanFull()
                ->rows(2),
            FileUpload::make('attachment_path')
                ->label(__('expenses.attachment'))
                ->disk('public')
                ->directory('expenses')
                ->columnSpanFull(),
        ]);
    }
}
