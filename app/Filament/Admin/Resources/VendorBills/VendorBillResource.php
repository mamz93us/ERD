<?php

declare(strict_types=1);

namespace App\Filament\Admin\Resources\VendorBills;

use App\Filament\Admin\Resources\VendorBills\Pages\CreateVendorBill;
use App\Filament\Admin\Resources\VendorBills\Pages\EditVendorBill;
use App\Filament\Admin\Resources\VendorBills\Pages\ListVendorBills;
use App\Filament\Admin\Resources\VendorBills\Schemas\VendorBillForm;
use App\Filament\Admin\Resources\VendorBills\Tables\VendorBillsTable;
use App\Models\VendorBill;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class VendorBillResource extends Resource
{
    protected static ?string $model = VendorBill::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedDocumentCurrencyDollar;

    public static function getNavigationLabel(): string
    {
        return __('navigation.vendor_bills');
    }

    public static function getNavigationGroup(): ?string
    {
        return __('navigation.accounting');
    }

    public static function getPluralModelLabel(): string
    {
        return __('navigation.vendor_bills');
    }

    public static function form(Schema $schema): Schema
    {
        return VendorBillForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return VendorBillsTable::configure($table);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListVendorBills::route('/'),
            'create' => CreateVendorBill::route('/create'),
            'edit' => EditVendorBill::route('/{record}/edit'),
        ];
    }
}
