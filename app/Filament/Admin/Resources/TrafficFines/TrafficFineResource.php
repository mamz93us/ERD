<?php

declare(strict_types=1);

namespace App\Filament\Admin\Resources\TrafficFines;

use App\Filament\Admin\Resources\TrafficFines\Pages\CreateTrafficFine;
use App\Filament\Admin\Resources\TrafficFines\Pages\EditTrafficFine;
use App\Filament\Admin\Resources\TrafficFines\Pages\ListTrafficFines;
use App\Filament\Admin\Resources\TrafficFines\Schemas\TrafficFineForm;
use App\Filament\Admin\Resources\TrafficFines\Tables\TrafficFinesTable;
use App\Models\TrafficFine;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class TrafficFineResource extends Resource
{
    protected static ?string $model = TrafficFine::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedExclamationTriangle;

    public static function getNavigationLabel(): string
    {
        return __('navigation.traffic_fines');
    }

    public static function getNavigationGroup(): ?string
    {
        return __('navigation.compliance');
    }

    public static function getPluralModelLabel(): string
    {
        return __('navigation.traffic_fines');
    }

    public static function form(Schema $schema): Schema
    {
        return TrafficFineForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return TrafficFinesTable::configure($table);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListTrafficFines::route('/'),
            'create' => CreateTrafficFine::route('/create'),
            'edit' => EditTrafficFine::route('/{record}/edit'),
        ];
    }
}
