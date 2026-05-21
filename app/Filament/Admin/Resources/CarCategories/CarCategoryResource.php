<?php

declare(strict_types=1);

namespace App\Filament\Admin\Resources\CarCategories;

use App\Filament\Admin\Resources\CarCategories\Pages\CreateCarCategory;
use App\Filament\Admin\Resources\CarCategories\Pages\EditCarCategory;
use App\Filament\Admin\Resources\CarCategories\Pages\ListCarCategories;
use App\Filament\Admin\Resources\CarCategories\Schemas\CarCategoryForm;
use App\Filament\Admin\Resources\CarCategories\Tables\CarCategoriesTable;
use App\Models\CarCategory;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class CarCategoryResource extends Resource
{
    protected static ?string $model = CarCategory::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedTag;

    public static function getNavigationLabel(): string
    {
        return __('navigation.car_categories');
    }

    public static function getNavigationGroup(): ?string
    {
        return __('navigation.fleet');
    }

    public static function getPluralModelLabel(): string
    {
        return __('navigation.car_categories');
    }

    public static function form(Schema $schema): Schema
    {
        return CarCategoryForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return CarCategoriesTable::configure($table);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListCarCategories::route('/'),
            'create' => CreateCarCategory::route('/create'),
            'edit' => EditCarCategory::route('/{record}/edit'),
        ];
    }
}
