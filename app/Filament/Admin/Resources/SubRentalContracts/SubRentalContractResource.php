<?php

declare(strict_types=1);

namespace App\Filament\Admin\Resources\SubRentalContracts;

use App\Filament\Admin\Resources\SubRentalContracts\Pages\CreateSubRentalContract;
use App\Filament\Admin\Resources\SubRentalContracts\Pages\EditSubRentalContract;
use App\Filament\Admin\Resources\SubRentalContracts\Pages\ListSubRentalContracts;
use App\Filament\Admin\Resources\SubRentalContracts\Schemas\SubRentalContractForm;
use App\Filament\Admin\Resources\SubRentalContracts\Tables\SubRentalContractsTable;
use App\Models\SubRentalContract;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class SubRentalContractResource extends Resource
{
    protected static ?string $model = SubRentalContract::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedDocumentText;

    public static function getNavigationLabel(): string
    {
        return __('navigation.sub_rental_contracts');
    }

    public static function getNavigationGroup(): ?string
    {
        return __('navigation.fleet');
    }

    public static function getPluralModelLabel(): string
    {
        return __('navigation.sub_rental_contracts');
    }

    public static function form(Schema $schema): Schema
    {
        return SubRentalContractForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return SubRentalContractsTable::configure($table);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListSubRentalContracts::route('/'),
            'create' => CreateSubRentalContract::route('/create'),
            'edit' => EditSubRentalContract::route('/{record}/edit'),
        ];
    }
}
