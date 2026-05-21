<?php

declare(strict_types=1);

namespace App\Filament\Admin\Resources\Branches\Schemas;

use App\Models\User;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class BranchForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema->components([
            TextInput::make('code')
                ->label(__('branches.code'))
                ->required()
                ->maxLength(16)
                ->unique(ignoreRecord: true),
            TextInput::make('name')
                ->label(__('branches.name'))
                ->required()
                ->maxLength(255),
            TextInput::make('name_ar')
                ->label(__('branches.name_ar'))
                ->required()
                ->maxLength(255),
            TextInput::make('city')
                ->label(__('branches.city'))
                ->maxLength(255),
            Textarea::make('address')
                ->label(__('branches.address'))
                ->rows(2),
            TextInput::make('phone')
                ->label(__('branches.phone'))
                ->tel()
                ->maxLength(255),
            Select::make('manager_user_id')
                ->label(__('branches.manager'))
                ->relationship('manager', 'full_name')
                ->options(fn () => User::query()->pluck('full_name', 'id'))
                ->searchable()
                ->nullable(),
        ]);
    }
}
