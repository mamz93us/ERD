<?php

declare(strict_types=1);

namespace App\Filament\Admin\Resources\PartnerAgencies;

use App\Filament\Admin\Resources\PartnerAgencies\Pages\CreatePartnerAgency;
use App\Filament\Admin\Resources\PartnerAgencies\Pages\EditPartnerAgency;
use App\Filament\Admin\Resources\PartnerAgencies\Pages\ListPartnerAgencies;
use App\Filament\Admin\Resources\PartnerAgencies\Schemas\PartnerAgencyForm;
use App\Filament\Admin\Resources\PartnerAgencies\Tables\PartnerAgenciesTable;
use App\Models\PartnerAgency;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class PartnerAgencyResource extends Resource
{
    protected static ?string $model = PartnerAgency::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedBriefcase;

    public static function getNavigationLabel(): string
    {
        return __('navigation.partner_agencies');
    }

    public static function getNavigationGroup(): ?string
    {
        return __('navigation.fleet');
    }

    public static function getPluralModelLabel(): string
    {
        return __('navigation.partner_agencies');
    }

    public static function form(Schema $schema): Schema
    {
        return PartnerAgencyForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return PartnerAgenciesTable::configure($table);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListPartnerAgencies::route('/'),
            'create' => CreatePartnerAgency::route('/create'),
            'edit' => EditPartnerAgency::route('/{record}/edit'),
        ];
    }

    public static function getRecordRouteBindingEloquentQuery(): Builder
    {
        return parent::getRecordRouteBindingEloquentQuery()
            ->withoutGlobalScopes([SoftDeletingScope::class]);
    }
}
