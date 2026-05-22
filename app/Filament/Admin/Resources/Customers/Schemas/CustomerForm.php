<?php

declare(strict_types=1);

namespace App\Filament\Admin\Resources\Customers\Schemas;

use App\Enums\CustomerType;
use App\Enums\PreferredLanguage;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;

class CustomerForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema->components([
            Select::make('corporate_account_id')
                ->label(__('customers.corporate_account'))
                ->relationship('corporateAccount', 'company_name')
                ->searchable()
                ->preload()
                ->nullable(),
            Select::make('type')
                ->label(__('customers.type'))
                ->options(CustomerType::class)
                ->default(CustomerType::Individual->value)
                ->required(),
            TextInput::make('full_name')
                ->label(__('customers.full_name'))
                ->required()
                ->maxLength(255),
            TextInput::make('full_name_ar')
                ->label(__('customers.full_name_ar'))
                ->maxLength(255),
            TextInput::make('phone')
                ->label(__('customers.phone'))
                ->tel()
                ->required()
                ->maxLength(255),
            TextInput::make('whatsapp_phone')
                ->label(__('customers.whatsapp_phone'))
                ->tel()
                ->maxLength(255),
            TextInput::make('email')
                ->label(__('customers.email'))
                ->email()
                ->maxLength(255),
            TextInput::make('password')
                ->label(__('customers.portal_password'))
                ->password()
                ->revealable()
                ->helperText(__('customers.portal_password_help'))
                ->maxLength(255)
                ->dehydrated(fn ($state) => filled($state)),
            TextInput::make('national_id')
                ->label(__('customers.national_id'))
                ->maxLength(255),
            Textarea::make('address')
                ->label(__('customers.address'))
                ->rows(2),
            Select::make('preferred_language')
                ->label(__('customers.preferred_language'))
                ->options(PreferredLanguage::class)
                ->default(PreferredLanguage::Ar->value)
                ->required(),
            TextInput::make('loyalty_points')
                ->label(__('customers.loyalty_points'))
                ->numeric()
                ->default(0),
            Toggle::make('is_blacklisted')
                ->label(__('customers.is_blacklisted'))
                ->live()
                ->default(false),
            Textarea::make('blacklist_reason')
                ->label(__('customers.blacklist_reason'))
                ->rows(2)
                ->visible(fn ($get) => (bool) $get('is_blacklisted')),
            Textarea::make('notes')
                ->label(__('customers.notes'))
                ->rows(3),
        ]);
    }
}
