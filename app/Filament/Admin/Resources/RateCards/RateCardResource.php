<?php

declare(strict_types=1);

namespace App\Filament\Admin\Resources\RateCards;

use App\Filament\Admin\Resources\RateCards\Pages\CreateRateCard;
use App\Filament\Admin\Resources\RateCards\Pages\EditRateCard;
use App\Filament\Admin\Resources\RateCards\Pages\ListRateCards;
use App\Filament\Admin\Resources\RateCards\Schemas\RateCardForm;
use App\Filament\Admin\Resources\RateCards\Tables\RateCardsTable;
use App\Models\RateCard;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class RateCardResource extends Resource
{
    protected static ?string $model = RateCard::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedCurrencyDollar;

    public static function getNavigationLabel(): string
    {
        return __('navigation.rate_cards');
    }

    public static function getNavigationGroup(): ?string
    {
        return __('navigation.pricing');
    }

    public static function getPluralModelLabel(): string
    {
        return __('navigation.rate_cards');
    }

    public static function form(Schema $schema): Schema
    {
        return RateCardForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return RateCardsTable::configure($table);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListRateCards::route('/'),
            'create' => CreateRateCard::route('/create'),
            'edit' => EditRateCard::route('/{record}/edit'),
        ];
    }
}
