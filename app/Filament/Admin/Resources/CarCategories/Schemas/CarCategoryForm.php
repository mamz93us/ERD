<?php

declare(strict_types=1);

namespace App\Filament\Admin\Resources\CarCategories\Schemas;

use App\Enums\CarCategoryClass;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class CarCategoryForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema->components([
            TextInput::make('name')
                ->label(__('car_categories.name'))
                ->required()
                ->maxLength(255),
            TextInput::make('name_ar')
                ->label(__('car_categories.name_ar'))
                ->required()
                ->maxLength(255),
            Select::make('class_code')
                ->label(__('car_categories.class_code'))
                ->options(CarCategoryClass::class)
                ->required(),
            TextInput::make('default_seats')
                ->label(__('car_categories.default_seats'))
                ->numeric()
                ->minValue(1)
                ->maxValue(64)
                ->default(5)
                ->required(),
            TextInput::make('sort_order')
                ->label(__('car_categories.sort_order'))
                ->numeric()
                ->default(0)
                ->required(),
        ]);
    }
}
