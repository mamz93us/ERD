<?php

declare(strict_types=1);

namespace App\Filament\Admin\Resources\Roles;

use App\Filament\Admin\Resources\Roles\Pages\EditRole;
use App\Filament\Admin\Resources\Roles\Pages\ListRoles;
use BackedEnum;
use Filament\Forms\Components\CheckboxList;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

/**
 * Phase 15: read + edit-permissions view over the 7 seeded roles
 * (super_admin, branch_manager, dispatcher, accountant, reservations_agent,
 * driver_supervisor, fleet_manager).
 *
 * No create/delete — roles are foundational and added via RoleSeeder. Only
 * super_admin can edit (policy attached in AdminPanelProvider).
 */
class RoleResource extends Resource
{
    protected static ?string $model = Role::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedShieldCheck;

    public static function getNavigationLabel(): string
    {
        return __('navigation.roles');
    }

    public static function getNavigationGroup(): ?string
    {
        return __('navigation.settings');
    }

    public static function getPluralModelLabel(): string
    {
        return __('navigation.roles');
    }

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            TextInput::make('name')
                ->label(__('roles.name'))
                ->disabled()
                ->dehydrated(false),
            TextInput::make('guard_name')
                ->label(__('roles.guard'))
                ->disabled()
                ->dehydrated(false),
            CheckboxList::make('permissions')
                ->label(__('roles.permissions'))
                ->relationship('permissions', 'name')
                ->options(fn () => Permission::query()->pluck('name', 'id'))
                ->bulkToggleable()
                ->columns(2)
                ->columnSpanFull()
                ->helperText(__('roles.permissions_help')),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->label(__('roles.name'))
                    ->searchable()
                    ->sortable()
                    ->weight('bold'),
                TextColumn::make('permissions_count')
                    ->counts('permissions')
                    ->label(__('roles.permissions_count')),
                TextColumn::make('users_count')
                    ->counts('users')
                    ->label(__('roles.users_count')),
                TextColumn::make('guard_name')
                    ->label(__('roles.guard'))
                    ->badge()
                    ->toggleable(),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListRoles::route('/'),
            'edit' => EditRole::route('/{record}/edit'),
        ];
    }

    public static function canCreate(): bool
    {
        return false;
    }
}
