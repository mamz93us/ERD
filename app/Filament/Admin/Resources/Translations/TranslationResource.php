<?php

declare(strict_types=1);

namespace App\Filament\Admin\Resources\Translations;

use App\Filament\Admin\Resources\Translations\Pages\CreateTranslation;
use App\Filament\Admin\Resources\Translations\Pages\EditTranslation;
use App\Filament\Admin\Resources\Translations\Pages\ListTranslations;
use App\Filament\Admin\Resources\Translations\Schemas\TranslationForm;
use App\Filament\Admin\Resources\Translations\Tables\TranslationsTable;
use App\Models\Translation;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class TranslationResource extends Resource
{
    protected static ?string $model = Translation::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedLanguage;

    public static function getNavigationLabel(): string
    {
        return __('navigation.translations');
    }

    public static function getModelLabel(): string
    {
        return __('translations.singular');
    }

    public static function getPluralModelLabel(): string
    {
        return __('navigation.translations');
    }

    public static function getNavigationGroup(): ?string
    {
        return __('navigation.settings');
    }

    public static function form(Schema $schema): Schema
    {
        return TranslationForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return TranslationsTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListTranslations::route('/'),
            'create' => CreateTranslation::route('/create'),
            'edit' => EditTranslation::route('/{record}/edit'),
        ];
    }
}
