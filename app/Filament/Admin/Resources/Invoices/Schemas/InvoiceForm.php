<?php

declare(strict_types=1);

namespace App\Filament\Admin\Resources\Invoices\Schemas;

use App\Enums\InvoiceStatus;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class InvoiceForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema->components([
            Placeholder::make('invoice_number')
                ->label(__('invoices.invoice_number'))
                ->content(fn ($record) => $record?->invoice_number ?? __('invoices.auto_generated_on_save'))
                ->columnSpan(1),
            Select::make('status')
                ->label(__('invoices.status'))
                ->options(InvoiceStatus::class)
                ->required()
                ->columnSpan(1),
            Select::make('customer_id')
                ->label(__('invoices.customer'))
                ->relationship('customer', 'full_name')
                ->searchable()
                ->preload()
                ->required(),
            Select::make('corporate_account_id')
                ->label(__('invoices.corporate_account'))
                ->relationship('corporateAccount', 'company_name')
                ->searchable()
                ->preload()
                ->nullable(),
            Select::make('trip_id')
                ->label(__('invoices.trip'))
                ->relationship('trip', 'trip_number')
                ->searchable()
                ->preload()
                ->nullable(),
            TextInput::make('currency')
                ->label(__('invoices.currency'))
                ->default('EGP')
                ->maxLength(3),
            DatePicker::make('issue_date')
                ->label(__('invoices.issue_date'))
                ->required(),
            DatePicker::make('due_date')
                ->label(__('invoices.due_date'))
                ->required(),
            TextInput::make('subtotal')
                ->label(__('invoices.subtotal'))
                ->numeric()
                ->prefix('EGP')
                ->required()
                ->default(0),
            TextInput::make('vat_amount')
                ->label(__('invoices.vat_amount'))
                ->numeric()
                ->prefix('EGP')
                ->required()
                ->default(0),
            TextInput::make('discount_amount')
                ->label(__('invoices.discount_amount'))
                ->numeric()
                ->prefix('EGP')
                ->default(0),
            TextInput::make('total')
                ->label(__('invoices.total'))
                ->numeric()
                ->prefix('EGP')
                ->required()
                ->default(0),
            TextInput::make('paid_amount')
                ->label(__('invoices.paid_amount'))
                ->numeric()
                ->prefix('EGP')
                ->default(0)
                ->disabled()
                ->dehydrated(false),
            TextInput::make('balance_due')
                ->label(__('invoices.balance_due'))
                ->numeric()
                ->prefix('EGP')
                ->default(0)
                ->disabled()
                ->dehydrated(false),
            Textarea::make('notes')
                ->label(__('invoices.notes'))
                ->columnSpanFull()
                ->rows(2),
            Textarea::make('terms')
                ->label(__('invoices.terms'))
                ->columnSpanFull()
                ->rows(2),
        ]);
    }
}
