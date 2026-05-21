<?php

declare(strict_types=1);

namespace App\Filament\Admin\Resources\Users\Schemas;

use App\Models\Branch;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;

class UserForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema->components([
            TextInput::make('full_name')
                ->label(__('users.full_name'))
                ->required()
                ->maxLength(255),
            TextInput::make('full_name_ar')
                ->label(__('users.full_name_ar'))
                ->maxLength(255),
            TextInput::make('email')
                ->label(__('users.email'))
                ->email()
                ->required()
                ->unique(ignoreRecord: true)
                ->maxLength(255),
            TextInput::make('phone')
                ->label(__('users.phone'))
                ->tel()
                ->maxLength(255),
            TextInput::make('password')
                ->label(__('users.password'))
                ->password()
                ->revealable()
                ->dehydrateStateUsing(fn ($state) => filled($state) ? Hash::make($state) : null)
                ->dehydrated(fn ($state) => filled($state))
                ->required(fn (string $operation) => $operation === 'create')
                ->minLength(8),
            Select::make('branch_id')
                ->label(__('users.branch'))
                ->options(fn () => Branch::query()->pluck('code', 'id'))
                ->searchable()
                ->nullable(),
            Select::make('preferred_locale')
                ->label(__('users.preferred_locale'))
                ->options(['ar' => 'العربية', 'en' => 'English'])
                ->default('ar')
                ->required(),
            Toggle::make('is_active')
                ->label(__('users.is_active'))
                ->default(true),
            Select::make('roles')
                ->label(__('users.roles'))
                ->options(fn () => Role::query()->pluck('name', 'name'))
                ->multiple()
                ->preload()
                ->saveRelationshipsUsing(function ($state, $record): void {
                    $record->syncRoles($state ?? []);
                })
                ->loadStateFromRelationshipsUsing(function ($component, $record): void {
                    $component->state($record->roles->pluck('name')->toArray());
                }),
        ]);
    }
}
