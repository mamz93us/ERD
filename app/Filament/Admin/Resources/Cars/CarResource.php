<?php

declare(strict_types=1);

namespace App\Filament\Admin\Resources\Cars;

use App\Filament\Admin\Resources\Cars\Pages\CreateCar;
use App\Filament\Admin\Resources\Cars\Pages\EditCar;
use App\Filament\Admin\Resources\Cars\Pages\ListCars;
use App\Filament\Admin\Resources\Cars\RelationManagers\DocumentsRelationManager;
use App\Filament\Admin\Resources\Cars\RelationManagers\SubRentalContractsRelationManager;
use App\Filament\Admin\Resources\Cars\Schemas\CarForm;
use App\Filament\Admin\Resources\Cars\Tables\CarsTable;
use App\Models\Car;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class CarResource extends Resource
{
    protected static ?string $model = Car::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedTruck;

    public static function getNavigationLabel(): string
    {
        return __('navigation.cars');
    }

    public static function getNavigationGroup(): ?string
    {
        return __('navigation.fleet');
    }

    public static function getPluralModelLabel(): string
    {
        return __('navigation.cars');
    }

    public static function form(Schema $schema): Schema
    {
        return CarForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return CarsTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [
            DocumentsRelationManager::class,
            SubRentalContractsRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListCars::route('/'),
            'create' => CreateCar::route('/create'),
            'edit' => EditCar::route('/{record}/edit'),
        ];
    }

    public static function getRecordRouteBindingEloquentQuery(): Builder
    {
        return parent::getRecordRouteBindingEloquentQuery()
            ->withoutGlobalScopes([SoftDeletingScope::class]);
    }
}
