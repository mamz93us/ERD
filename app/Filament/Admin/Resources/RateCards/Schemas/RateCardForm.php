<?php

declare(strict_types=1);

namespace App\Filament\Admin\Resources\RateCards\Schemas;

use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;

class RateCardForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema->components([
            Select::make('category_id')
                ->label(__('rate_cards.category'))
                ->relationship('category', 'name')
                ->required(),
            Select::make('corporate_account_id')
                ->label(__('rate_cards.corporate_account'))
                ->relationship('corporateAccount', 'company_name')
                ->searchable()
                ->nullable()
                ->helperText(__('rate_cards.corporate_account_help')),
            TextInput::make('name')
                ->label(__('rate_cards.name'))
                ->required()
                ->maxLength(255),
            TextInput::make('hourly_rate')
                ->label(__('rate_cards.hourly_rate'))
                ->numeric()
                ->prefix('EGP')
                ->required(),
            TextInput::make('daily_rate')
                ->label(__('rate_cards.daily_rate'))
                ->numeric()
                ->prefix('EGP')
                ->required(),
            TextInput::make('weekly_rate')
                ->label(__('rate_cards.weekly_rate'))
                ->numeric()
                ->prefix('EGP')
                ->required(),
            TextInput::make('monthly_rate')
                ->label(__('rate_cards.monthly_rate'))
                ->numeric()
                ->prefix('EGP')
                ->required(),
            TextInput::make('included_km_per_day')
                ->label(__('rate_cards.included_km_per_day'))
                ->numeric()
                ->suffix(' km')
                ->required(),
            TextInput::make('extra_km_rate')
                ->label(__('rate_cards.extra_km_rate'))
                ->numeric()
                ->prefix('EGP'),
            TextInput::make('extra_hour_rate')
                ->label(__('rate_cards.extra_hour_rate'))
                ->numeric()
                ->prefix('EGP'),
            TextInput::make('driver_daily_allowance')
                ->label(__('rate_cards.driver_daily_allowance'))
                ->numeric()
                ->prefix('EGP'),
            TextInput::make('cross_city_surcharge')
                ->label(__('rate_cards.cross_city_surcharge'))
                ->numeric()
                ->prefix('EGP'),
            DatePicker::make('effective_from')
                ->label(__('rate_cards.effective_from'))
                ->required(),
            DatePicker::make('effective_to')
                ->label(__('rate_cards.effective_to'))
                ->after('effective_from'),
            Toggle::make('is_active')
                ->label(__('rate_cards.is_active'))
                ->default(true),
        ]);
    }
}
