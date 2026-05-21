<?php

declare(strict_types=1);

namespace App\Filament\Admin\Resources\Quotations\Schemas;

use App\Enums\QuotationStatus;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Component;
use Filament\Schemas\Components\Tabs;
use Filament\Schemas\Components\Tabs\Tab;
use Filament\Schemas\Schema;

class QuotationForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema->components([
            Tabs::make()
                ->columnSpanFull()
                ->tabs([
                    Tab::make(__('quotations.tabs.customer_trip'))
                        ->icon('heroicon-o-user')
                        ->schema(self::customerTripFields()),
                    Tab::make(__('quotations.tabs.pricing'))
                        ->icon('heroicon-o-currency-dollar')
                        ->schema(self::pricingFields()),
                    Tab::make(__('quotations.tabs.notes'))
                        ->icon('heroicon-o-document-text')
                        ->schema(self::notesFields()),
                ]),
        ]);
    }

    /** @return list<Component> */
    private static function customerTripFields(): array
    {
        return [
            Select::make('customer_id')
                ->label(__('quotations.customer'))
                ->relationship('customer', 'full_name')
                ->searchable()
                ->required(),
            Select::make('corporate_account_id')
                ->label(__('quotations.corporate_account'))
                ->relationship('corporateAccount', 'company_name')
                ->searchable()
                ->nullable(),
            Select::make('category_id')
                ->label(__('quotations.category'))
                ->relationship('category', 'name')
                ->required(),
            DateTimePicker::make('pickup_at')
                ->label(__('quotations.pickup_at'))
                ->required()
                ->seconds(false),
            DateTimePicker::make('dropoff_at')
                ->label(__('quotations.dropoff_at'))
                ->required()
                ->after('pickup_at')
                ->seconds(false),
            TextInput::make('pickup_location')
                ->label(__('quotations.pickup_location'))
                ->required()
                ->maxLength(255),
            TextInput::make('dropoff_location')
                ->label(__('quotations.dropoff_location'))
                ->required()
                ->maxLength(255),
            TextInput::make('estimated_distance_km')
                ->label(__('quotations.estimated_distance_km'))
                ->numeric()
                ->suffix(' km')
                ->default(0),
        ];
    }

    /** @return list<Component> */
    private static function pricingFields(): array
    {
        return [
            Placeholder::make('pricing_help')
                ->label('')
                ->content(__('quotations.pricing_help'))
                ->columnSpanFull(),
            Select::make('rate_card_id')
                ->label(__('quotations.rate_card'))
                ->relationship('rateCard', 'name')
                ->searchable()
                ->nullable()
                ->helperText(__('quotations.rate_card_help')),
            TextInput::make('subtotal')
                ->label(__('quotations.subtotal'))
                ->numeric()
                ->prefix('EGP')
                ->readOnly(),
            TextInput::make('vat_amount')
                ->label(__('quotations.vat_amount'))
                ->numeric()
                ->prefix('EGP')
                ->readOnly(),
            TextInput::make('total_amount')
                ->label(__('quotations.total_amount'))
                ->numeric()
                ->prefix('EGP')
                ->readOnly(),
            DateTimePicker::make('valid_until')
                ->label(__('quotations.valid_until'))
                ->required()
                ->default(now()->addDays(7))
                ->seconds(false),
            Select::make('status')
                ->label(__('quotations.status'))
                ->options(QuotationStatus::class)
                ->default(QuotationStatus::Draft->value)
                ->required(),
        ];
    }

    /** @return list<Component> */
    private static function notesFields(): array
    {
        return [
            Textarea::make('notes')
                ->label(__('quotations.notes'))
                ->rows(3)
                ->columnSpanFull(),
            Textarea::make('terms_and_conditions')
                ->label(__('quotations.terms_and_conditions'))
                ->rows(5)
                ->columnSpanFull(),
        ];
    }
}
