<?php

declare(strict_types=1);

namespace App\Filament\Admin\Resources\Translations\Schemas;

use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;

class TranslationForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema->components([
            TextInput::make('group')
                ->label(__('translations.group'))
                ->required()
                ->maxLength(255)
                ->disabled(fn ($record) => (bool) $record?->is_system),
            TextInput::make('key')
                ->label(__('translations.key'))
                ->required()
                ->maxLength(255)
                ->disabled(fn ($record) => (bool) $record?->is_system),
            Textarea::make('text_ar')
                ->label(__('translations.text_ar'))
                ->rows(3),
            Textarea::make('text_en')
                ->label(__('translations.text_en'))
                ->rows(3),
            Toggle::make('is_system')
                ->label(__('translations.is_system'))
                ->disabled()
                ->dehydrated(),
        ]);
    }
}
