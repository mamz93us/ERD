<?php

declare(strict_types=1);

namespace App\Filament\Admin\Resources\PartnerAgencies\Schemas;

use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;

class PartnerAgencyForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema->components([
            TextInput::make('name')
                ->label(__('partner_agencies.name'))
                ->required()
                ->maxLength(255),
            TextInput::make('name_ar')
                ->label(__('partner_agencies.name_ar'))
                ->required()
                ->maxLength(255),
            TextInput::make('contact_person')
                ->label(__('partner_agencies.contact_person'))
                ->maxLength(255),
            TextInput::make('phone')
                ->label(__('partner_agencies.phone'))
                ->tel()
                ->maxLength(255),
            TextInput::make('email')
                ->label(__('partner_agencies.email'))
                ->email()
                ->maxLength(255),
            TextInput::make('tax_id')
                ->label(__('partner_agencies.tax_id'))
                ->maxLength(255),
            Textarea::make('address')
                ->label(__('partner_agencies.address'))
                ->rows(2),
            TextInput::make('credit_limit')
                ->label(__('partner_agencies.credit_limit'))
                ->numeric()
                ->prefix('EGP')
                ->default(0)
                ->required(),
            TextInput::make('payment_terms_days')
                ->label(__('partner_agencies.payment_terms_days'))
                ->numeric()
                ->default(0)
                ->required(),
            Toggle::make('is_active')
                ->label(__('partner_agencies.is_active'))
                ->default(true),
        ]);
    }
}
