<?php

declare(strict_types=1);

namespace App\Filament\Admin\Resources\SubRentalContracts\Schemas;

use App\Enums\SubRentalContractStatus;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class SubRentalContractForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema->components([
            Select::make('partner_agency_id')
                ->label(__('sub_rental_contracts.partner_agency'))
                ->relationship('partnerAgency', 'name')
                ->searchable()
                ->required(),
            Select::make('car_id')
                ->label(__('sub_rental_contracts.car'))
                ->relationship('car', 'plate')
                ->searchable()
                ->required(),
            DatePicker::make('start_date')
                ->label(__('sub_rental_contracts.start_date'))
                ->required(),
            DatePicker::make('end_date')
                ->label(__('sub_rental_contracts.end_date'))
                ->required()
                ->afterOrEqual('start_date'),
            TextInput::make('daily_cost')
                ->label(__('sub_rental_contracts.daily_cost'))
                ->numeric()
                ->prefix('EGP')
                ->required(),
            TextInput::make('included_km_per_day')
                ->label(__('sub_rental_contracts.included_km_per_day'))
                ->numeric()
                ->suffix(' km'),
            TextInput::make('extra_km_cost')
                ->label(__('sub_rental_contracts.extra_km_cost'))
                ->numeric()
                ->prefix('EGP'),
            Textarea::make('terms')
                ->label(__('sub_rental_contracts.terms'))
                ->rows(3)
                ->columnSpanFull(),
            Select::make('status')
                ->label(__('sub_rental_contracts.status'))
                ->options(SubRentalContractStatus::class)
                ->default(SubRentalContractStatus::Active->value)
                ->required(),
            FileUpload::make('contract_file_path')
                ->label(__('sub_rental_contracts.contract_file'))
                ->disk('public')
                ->directory('sub-rental-contracts'),
        ]);
    }
}
