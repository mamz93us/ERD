<?php

declare(strict_types=1);

namespace App\Filament\Admin\Resources\CorporateAccounts\Schemas;

use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;

class CorporateAccountForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema->components([
            TextInput::make('company_name')
                ->label(__('corporate_accounts.company_name'))
                ->required()
                ->maxLength(255),
            TextInput::make('company_name_ar')
                ->label(__('corporate_accounts.company_name_ar'))
                ->maxLength(255),
            TextInput::make('tax_id')
                ->label(__('corporate_accounts.tax_id'))
                ->maxLength(255),
            TextInput::make('commercial_register')
                ->label(__('corporate_accounts.commercial_register'))
                ->maxLength(255),
            TextInput::make('industry')
                ->label(__('corporate_accounts.industry'))
                ->maxLength(255),
            Textarea::make('address')
                ->label(__('corporate_accounts.address'))
                ->rows(2),
            TextInput::make('billing_email')
                ->label(__('corporate_accounts.billing_email'))
                ->email()
                ->maxLength(255),
            TextInput::make('billing_phone')
                ->label(__('corporate_accounts.billing_phone'))
                ->tel()
                ->maxLength(255),
            TextInput::make('credit_limit')
                ->label(__('corporate_accounts.credit_limit'))
                ->numeric()
                ->prefix('EGP')
                ->default(0)
                ->required(),
            TextInput::make('payment_terms_days')
                ->label(__('corporate_accounts.payment_terms_days'))
                ->numeric()
                ->default(0)
                ->required(),
            TextInput::make('discount_percentage')
                ->label(__('corporate_accounts.discount_percentage'))
                ->numeric()
                ->suffix('%')
                ->default(0)
                ->required(),
            Toggle::make('is_active')
                ->label(__('corporate_accounts.is_active'))
                ->default(true),
            Textarea::make('notes')
                ->label(__('corporate_accounts.notes'))
                ->rows(3),
        ]);
    }
}
