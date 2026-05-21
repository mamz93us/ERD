<?php

declare(strict_types=1);

namespace App\Filament\Admin\Resources\Garages;

use App\Filament\Admin\Resources\Garages\Pages\CreateGarage;
use App\Filament\Admin\Resources\Garages\Pages\EditGarage;
use App\Filament\Admin\Resources\Garages\Pages\ListGarages;
use App\Filament\Admin\Resources\Garages\Schemas\GarageForm;
use App\Filament\Admin\Resources\Garages\Tables\GaragesTable;
use App\Models\Garage;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class GarageResource extends Resource
{
    protected static ?string $model = Garage::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedWrenchScrewdriver;

    public static function getNavigationLabel(): string
    {
        return __('navigation.garages');
    }

    public static function getNavigationGroup(): ?string
    {
        return __('navigation.fleet');
    }

    public static function getPluralModelLabel(): string
    {
        return __('navigation.garages');
    }

    public static function form(Schema $schema): Schema
    {
        return GarageForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return GaragesTable::configure($table);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListGarages::route('/'),
            'create' => CreateGarage::route('/create'),
            'edit' => EditGarage::route('/{record}/edit'),
        ];
    }
}
