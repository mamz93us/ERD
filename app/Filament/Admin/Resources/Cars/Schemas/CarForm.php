<?php

declare(strict_types=1);

namespace App\Filament\Admin\Resources\Cars\Schemas;

use App\Enums\CarFuelType;
use App\Enums\CarOwnershipType;
use App\Enums\CarStatus;
use App\Enums\CarTransmission;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Component;
use Filament\Schemas\Components\Tabs;
use Filament\Schemas\Components\Tabs\Tab;
use Filament\Schemas\Schema;
use Illuminate\Support\HtmlString;

class CarForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema->components([
            Tabs::make()
                ->columnSpanFull()
                ->tabs([
                    Tab::make(__('cars.tabs.basic_info'))
                        ->icon('heroicon-o-identification')
                        ->schema(self::basicInfoFields()),
                    Tab::make(__('cars.tabs.damage_map'))
                        ->icon('heroicon-o-bug-ant')
                        ->schema([
                            Placeholder::make('damage_map_placeholder')
                                ->label('')
                                ->content(fn () => new HtmlString(
                                    '<p class="text-sm text-gray-500">'
                                    .__('cars.tabs.damage_map_placeholder')
                                    .'</p>'
                                )),
                        ]),
                    Tab::make(__('cars.tabs.service_history'))
                        ->icon('heroicon-o-wrench-screwdriver')
                        ->schema([
                            Placeholder::make('service_history_placeholder')
                                ->label('')
                                ->content(fn () => new HtmlString(
                                    '<p class="text-sm text-gray-500">'
                                    .__('cars.tabs.service_history_placeholder')
                                    .'</p>'
                                )),
                        ]),
                ]),
        ]);
    }

    /** @return list<Component> */
    private static function basicInfoFields(): array
    {
        return [
            Select::make('branch_id')
                ->label(__('cars.branch'))
                ->relationship('branch', 'code')
                ->required(),
            Select::make('category_id')
                ->label(__('cars.category'))
                ->relationship('category', 'name')
                ->required(),
            TextInput::make('plate')
                ->label(__('cars.plate'))
                ->required()
                ->unique(ignoreRecord: true)
                ->maxLength(255),
            TextInput::make('vin')
                ->label(__('cars.vin'))
                ->unique(ignoreRecord: true)
                ->maxLength(255),
            TextInput::make('make')
                ->label(__('cars.make'))
                ->required()
                ->maxLength(255),
            TextInput::make('model')
                ->label(__('cars.model'))
                ->required()
                ->maxLength(255),
            TextInput::make('year')
                ->label(__('cars.year'))
                ->numeric()
                ->minValue(1990)
                ->maxValue((int) date('Y') + 1)
                ->required(),
            TextInput::make('color')
                ->label(__('cars.color'))
                ->maxLength(255),
            Select::make('transmission')
                ->label(__('cars.transmission'))
                ->options(CarTransmission::class)
                ->default(CarTransmission::Auto->value)
                ->required(),
            Select::make('fuel_type')
                ->label(__('cars.fuel_type'))
                ->options(CarFuelType::class)
                ->default(CarFuelType::Petrol->value)
                ->required(),
            TextInput::make('seats')
                ->label(__('cars.seats'))
                ->numeric()
                ->minValue(2)
                ->maxValue(64)
                ->default(5)
                ->required(),
            Select::make('ownership_type')
                ->label(__('cars.ownership_type'))
                ->options(CarOwnershipType::class)
                ->default(CarOwnershipType::Owned->value)
                ->required(),
            Select::make('status')
                ->label(__('cars.status'))
                ->options(CarStatus::class)
                ->default(CarStatus::Available->value)
                ->required(),
            TextInput::make('current_odometer')
                ->label(__('cars.current_odometer'))
                ->numeric()
                ->suffix('km')
                ->default(0)
                ->required(),
            TextInput::make('acquisition_date')
                ->label(__('cars.acquisition_date'))
                ->type('date'),
            TextInput::make('acquisition_cost')
                ->label(__('cars.acquisition_cost'))
                ->numeric()
                ->prefix('EGP'),
            Textarea::make('notes')
                ->label(__('cars.notes'))
                ->rows(3)
                ->columnSpanFull(),
        ];
    }
}
