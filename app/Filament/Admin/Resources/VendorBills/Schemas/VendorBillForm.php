<?php

declare(strict_types=1);

namespace App\Filament\Admin\Resources\VendorBills\Schemas;

use App\Enums\VendorBillStatus;
use App\Enums\VendorType;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class VendorBillForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema->components([
            TextInput::make('bill_number')
                ->label(__('vendor_bills.bill_number'))
                ->maxLength(255)
                ->helperText(__('vendor_bills.bill_number_help')),
            Select::make('vendor_type')
                ->label(__('vendor_bills.vendor_type'))
                ->options(VendorType::class)
                ->live()
                ->required(),
            Select::make('partner_agency_id')
                ->label(__('vendor_bills.partner_agency'))
                ->relationship('partnerAgency', 'name')
                ->searchable()
                ->preload()
                ->visible(fn ($get) => $get('vendor_type') === VendorType::PartnerAgency->value),
            Select::make('garage_id')
                ->label(__('vendor_bills.garage'))
                ->relationship('garage', 'name')
                ->searchable()
                ->preload()
                ->visible(fn ($get) => $get('vendor_type') === VendorType::Garage->value),
            Select::make('related_car_id')
                ->label(__('vendor_bills.related_car'))
                ->relationship('relatedCar', 'plate')
                ->searchable()
                ->preload()
                ->nullable(),
            Select::make('related_sub_rental_contract_id')
                ->label(__('vendor_bills.related_sub_rental_contract'))
                ->relationship('relatedSubRentalContract', 'id')
                ->searchable()
                ->preload()
                ->nullable(),
            DatePicker::make('bill_date')
                ->label(__('vendor_bills.bill_date'))
                ->required(),
            DatePicker::make('due_date')
                ->label(__('vendor_bills.due_date'))
                ->nullable(),
            TextInput::make('subtotal')
                ->label(__('vendor_bills.subtotal'))
                ->numeric()
                ->prefix('EGP')
                ->required()
                ->default(0),
            TextInput::make('vat_amount')
                ->label(__('vendor_bills.vat_amount'))
                ->numeric()
                ->prefix('EGP')
                ->default(0),
            TextInput::make('total')
                ->label(__('vendor_bills.total'))
                ->numeric()
                ->prefix('EGP')
                ->required()
                ->default(0),
            TextInput::make('paid_amount')
                ->label(__('vendor_bills.paid_amount'))
                ->numeric()
                ->prefix('EGP')
                ->default(0),
            TextInput::make('balance_due')
                ->label(__('vendor_bills.balance_due'))
                ->numeric()
                ->prefix('EGP')
                ->default(0),
            Select::make('status')
                ->label(__('vendor_bills.status'))
                ->options(VendorBillStatus::class)
                ->required(),
            Textarea::make('description')
                ->label(__('vendor_bills.description'))
                ->columnSpanFull()
                ->rows(2),
            FileUpload::make('attachment_path')
                ->label(__('vendor_bills.attachment'))
                ->disk('public')
                ->directory('vendor-bills')
                ->columnSpanFull(),
        ]);
    }
}
