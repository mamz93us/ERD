<?php

declare(strict_types=1);

namespace App\Filament\Admin\Resources\Drivers;

use App\Filament\Admin\Resources\Drivers\Pages\CreateDriver;
use App\Filament\Admin\Resources\Drivers\Pages\EditDriver;
use App\Filament\Admin\Resources\Drivers\Pages\ListDrivers;
use App\Filament\Admin\Resources\Drivers\RelationManagers\DocumentsRelationManager;
use App\Filament\Admin\Resources\Drivers\Schemas\DriverForm;
use App\Filament\Admin\Resources\Drivers\Tables\DriversTable;
use App\Models\Driver;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class DriverResource extends Resource
{
    protected static ?string $model = Driver::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedIdentification;

    public static function getNavigationLabel(): string
    {
        return __('navigation.drivers');
    }

    public static function getNavigationGroup(): ?string
    {
        return __('navigation.fleet');
    }

    public static function getPluralModelLabel(): string
    {
        return __('navigation.drivers');
    }

    public static function form(Schema $schema): Schema
    {
        return DriverForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return DriversTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [
            DocumentsRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListDrivers::route('/'),
            'create' => CreateDriver::route('/create'),
            'edit' => EditDriver::route('/{record}/edit'),
        ];
    }

    public static function getRecordRouteBindingEloquentQuery(): Builder
    {
        return parent::getRecordRouteBindingEloquentQuery()
            ->withoutGlobalScopes([SoftDeletingScope::class]);
    }
}
