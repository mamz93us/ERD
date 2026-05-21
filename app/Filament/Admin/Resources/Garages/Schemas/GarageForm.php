<?php

declare(strict_types=1);

namespace App\Filament\Admin\Resources\Garages\Schemas;

use Filament\Forms\Components\TagsInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;

class GarageForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema->components([
            TextInput::make('name')
                ->label(__('garages.name'))
                ->required()
                ->maxLength(255),
            TextInput::make('phone')
                ->label(__('garages.phone'))
                ->tel()
                ->maxLength(255),
            Textarea::make('address')
                ->label(__('garages.address'))
                ->rows(2),
            Toggle::make('is_internal')
                ->label(__('garages.is_internal'))
                ->default(false),
            TagsInput::make('specialties')
                ->label(__('garages.specialties'))
                ->placeholder(__('garages.specialties_placeholder')),
            Toggle::make('is_active')
                ->label(__('garages.is_active'))
                ->default(true),
        ]);
    }
}
