<?php

declare(strict_types=1);

namespace App\Filament\Admin\Resources\Payments\Schemas;

use App\Enums\PaymentMethod;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;

class PaymentForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema->components([
            Placeholder::make('payment_number')
                ->label(__('payments.payment_number'))
                ->content(fn ($record) => $record?->payment_number ?? __('payments.auto_generated_on_save')),
            Select::make('method')
                ->label(__('payments.method'))
                ->options(PaymentMethod::class)
                ->required(),
            Select::make('customer_id')
                ->label(__('payments.customer'))
                ->relationship('customer', 'full_name')
                ->searchable()
                ->preload()
                ->required(),
            Select::make('corporate_account_id')
                ->label(__('payments.corporate_account'))
                ->relationship('corporateAccount', 'company_name')
                ->searchable()
                ->preload()
                ->nullable(),
            TextInput::make('amount')
                ->label(__('payments.amount'))
                ->numeric()
                ->prefix('EGP')
                ->required(),
            DatePicker::make('payment_date')
                ->label(__('payments.payment_date'))
                ->required()
                ->default(now()),
            TextInput::make('reference_number')
                ->label(__('payments.reference_number'))
                ->maxLength(255)
                ->nullable(),
            Select::make('branch_id')
                ->label(__('payments.branch'))
                ->relationship('branch', 'code')
                ->required(),
            Select::make('received_by_user_id')
                ->label(__('payments.received_by'))
                ->relationship('receivedBy', 'full_name')
                ->searchable()
                ->preload()
                ->default(auth()->id())
                ->required(),
            Toggle::make('is_reconciled')
                ->label(__('payments.is_reconciled'))
                ->default(false),
            Textarea::make('notes')
                ->label(__('payments.notes'))
                ->rows(2)
                ->columnSpanFull(),
        ]);
    }
}
