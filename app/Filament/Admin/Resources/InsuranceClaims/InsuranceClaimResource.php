<?php

declare(strict_types=1);

namespace App\Filament\Admin\Resources\InsuranceClaims;

use App\Filament\Admin\Resources\InsuranceClaims\Pages\CreateInsuranceClaim;
use App\Filament\Admin\Resources\InsuranceClaims\Pages\EditInsuranceClaim;
use App\Filament\Admin\Resources\InsuranceClaims\Pages\ListInsuranceClaims;
use App\Filament\Admin\Resources\InsuranceClaims\Schemas\InsuranceClaimForm;
use App\Filament\Admin\Resources\InsuranceClaims\Tables\InsuranceClaimsTable;
use App\Models\InsuranceClaim;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class InsuranceClaimResource extends Resource
{
    protected static ?string $model = InsuranceClaim::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedShieldCheck;

    public static function getNavigationLabel(): string
    {
        return __('navigation.insurance_claims');
    }

    public static function getNavigationGroup(): ?string
    {
        return __('navigation.compliance');
    }

    public static function getPluralModelLabel(): string
    {
        return __('navigation.insurance_claims');
    }

    public static function form(Schema $schema): Schema
    {
        return InsuranceClaimForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return InsuranceClaimsTable::configure($table);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListInsuranceClaims::route('/'),
            'create' => CreateInsuranceClaim::route('/create'),
            'edit' => EditInsuranceClaim::route('/{record}/edit'),
        ];
    }
}
