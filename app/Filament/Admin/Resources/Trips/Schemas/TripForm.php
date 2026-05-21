<?php

declare(strict_types=1);

namespace App\Filament\Admin\Resources\Trips\Schemas;

use App\Enums\EgyptianGovernorate;
use App\Enums\TripStatus;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Component;
use Filament\Schemas\Components\Tabs;
use Filament\Schemas\Components\Tabs\Tab;
use Filament\Schemas\Schema;

class TripForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema->components([
            Tabs::make()
                ->columnSpanFull()
                ->tabs([
                    Tab::make(__('trips.tabs.booking'))
                        ->icon('heroicon-o-calendar')
                        ->schema(self::bookingFields()),
                    Tab::make(__('trips.tabs.pricing'))
                        ->icon('heroicon-o-currency-dollar')
                        ->schema(self::pricingFields()),
                    Tab::make(__('trips.tabs.notes'))
                        ->icon('heroicon-o-document-text')
                        ->schema(self::notesFields()),
                ]),
        ]);
    }

    /** @return list<Component> */
    private static function bookingFields(): array
    {
        return [
            Select::make('branch_id')
                ->label(__('trips.branch'))
                ->relationship('branch', 'code')
                ->searchable()
                ->preload()
                ->required(),
            Select::make('customer_id')
                ->label(__('trips.customer'))
                ->relationship('customer', 'full_name')
                ->searchable()
                ->preload()
                ->required(),
            Select::make('corporate_account_id')
                ->label(__('trips.corporate_account'))
                ->relationship('corporateAccount', 'company_name')
                ->searchable()
                ->preload()
                ->nullable(),
            Select::make('car_id')
                ->label(__('trips.car'))
                ->relationship('car', 'plate')
                ->searchable()
                ->preload()
                ->required(),
            Select::make('driver_id')
                ->label(__('trips.driver'))
                ->relationship('driver', 'full_name')
                ->searchable()
                ->preload()
                ->required()
                ->helperText(__('trips.driver_help')),
            Select::make('rate_card_id')
                ->label(__('trips.rate_card'))
                ->relationship('rateCard', 'name')
                ->searchable()
                ->preload()
                ->required(),
            DateTimePicker::make('scheduled_start')
                ->label(__('trips.scheduled_start'))
                ->required()
                ->seconds(false),
            DateTimePicker::make('scheduled_end')
                ->label(__('trips.scheduled_end'))
                ->required()
                ->after('scheduled_start')
                ->seconds(false),
            Select::make('pickup_location')
                ->label(__('trips.pickup_location'))
                ->options(EgyptianGovernorate::class)
                ->searchable()
                ->required()
                ->helperText(__('trips.governorate_help')),
            Select::make('dropoff_location')
                ->label(__('trips.dropoff_location'))
                ->options(EgyptianGovernorate::class)
                ->searchable()
                ->required(),
            Select::make('status')
                ->label(__('trips.status'))
                ->options(TripStatus::class)
                ->default(TripStatus::Draft->value)
                ->required()
                ->disabled(fn ($context) => $context === 'create')
                ->helperText(__('trips.status_help')),
        ];
    }

    /** @return list<Component> */
    private static function pricingFields(): array
    {
        return [
            TextInput::make('subtotal')
                ->label(__('trips.subtotal'))
                ->numeric()
                ->prefix('EGP')
                ->default(0),
            TextInput::make('vat_amount')
                ->label(__('trips.vat_amount'))
                ->numeric()
                ->prefix('EGP')
                ->default(0),
            TextInput::make('total_amount')
                ->label(__('trips.total_amount'))
                ->numeric()
                ->prefix('EGP')
                ->default(0),
        ];
    }

    /** @return list<Component> */
    private static function notesFields(): array
    {
        return [
            Textarea::make('notes')
                ->label(__('trips.notes'))
                ->rows(3)
                ->columnSpanFull(),
            Textarea::make('cancellation_reason')
                ->label(__('trips.cancellation_reason'))
                ->rows(2)
                ->columnSpanFull()
                ->visible(fn ($record) => $record !== null
                    && in_array($record->status?->value ?? null, ['cancelled', 'no_show'], true)),
        ];
    }
}
