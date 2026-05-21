<?php

declare(strict_types=1);

namespace App\Filament\Admin\Resources\MaintenanceOrders;

use App\Filament\Admin\Resources\MaintenanceOrders\Pages\CreateMaintenanceOrder;
use App\Filament\Admin\Resources\MaintenanceOrders\Pages\EditMaintenanceOrder;
use App\Filament\Admin\Resources\MaintenanceOrders\Pages\ListMaintenanceOrders;
use App\Filament\Admin\Resources\MaintenanceOrders\RelationManagers\ItemsRelationManager;
use App\Filament\Admin\Resources\MaintenanceOrders\Schemas\MaintenanceOrderForm;
use App\Filament\Admin\Resources\MaintenanceOrders\Tables\MaintenanceOrdersTable;
use App\Models\MaintenanceOrder;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class MaintenanceOrderResource extends Resource
{
    protected static ?string $model = MaintenanceOrder::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedWrench;

    public static function getNavigationLabel(): string
    {
        return __('navigation.maintenance_orders');
    }

    public static function getNavigationGroup(): ?string
    {
        return __('navigation.maintenance');
    }

    public static function getPluralModelLabel(): string
    {
        return __('navigation.maintenance_orders');
    }

    public static function form(Schema $schema): Schema
    {
        return MaintenanceOrderForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return MaintenanceOrdersTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [
            ItemsRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListMaintenanceOrders::route('/'),
            'create' => CreateMaintenanceOrder::route('/create'),
            'edit' => EditMaintenanceOrder::route('/{record}/edit'),
        ];
    }

    public static function getRecordRouteBindingEloquentQuery(): Builder
    {
        return parent::getRecordRouteBindingEloquentQuery()
            ->withoutGlobalScopes([SoftDeletingScope::class]);
    }
}
