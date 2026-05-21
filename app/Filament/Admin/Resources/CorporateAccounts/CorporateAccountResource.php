<?php

declare(strict_types=1);

namespace App\Filament\Admin\Resources\CorporateAccounts;

use App\Filament\Admin\Resources\CorporateAccounts\Pages\CreateCorporateAccount;
use App\Filament\Admin\Resources\CorporateAccounts\Pages\EditCorporateAccount;
use App\Filament\Admin\Resources\CorporateAccounts\Pages\ListCorporateAccounts;
use App\Filament\Admin\Resources\CorporateAccounts\Schemas\CorporateAccountForm;
use App\Filament\Admin\Resources\CorporateAccounts\Tables\CorporateAccountsTable;
use App\Models\CorporateAccount;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class CorporateAccountResource extends Resource
{
    protected static ?string $model = CorporateAccount::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedBuildingOffice;

    public static function getNavigationLabel(): string
    {
        return __('navigation.corporate_accounts');
    }

    public static function getNavigationGroup(): ?string
    {
        return __('navigation.crm');
    }

    public static function getPluralModelLabel(): string
    {
        return __('navigation.corporate_accounts');
    }

    public static function form(Schema $schema): Schema
    {
        return CorporateAccountForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return CorporateAccountsTable::configure($table);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListCorporateAccounts::route('/'),
            'create' => CreateCorporateAccount::route('/create'),
            'edit' => EditCorporateAccount::route('/{record}/edit'),
        ];
    }

    public static function getRecordRouteBindingEloquentQuery(): Builder
    {
        return parent::getRecordRouteBindingEloquentQuery()
            ->withoutGlobalScopes([SoftDeletingScope::class]);
    }
}
