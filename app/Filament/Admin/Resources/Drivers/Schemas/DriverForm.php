<?php

declare(strict_types=1);

namespace App\Filament\Admin\Resources\Drivers\Schemas;

use App\Enums\DriverStatus;
use App\Enums\EmploymentType;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class DriverForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema->components([
            Select::make('branch_id')
                ->label(__('drivers.branch'))
                ->relationship('branch', 'code')
                ->searchable()
                ->preload()
                ->required(),
            TextInput::make('national_id')
                ->label(__('drivers.national_id'))
                ->required()
                ->unique(ignoreRecord: true)
                ->maxLength(255),
            TextInput::make('password')
                ->label(__('drivers.password'))
                ->password()
                ->revealable()
                ->helperText(__('drivers.password_help'))
                ->maxLength(255)
                ->dehydrated(fn ($state) => filled($state))
                ->required(fn (string $context): bool => $context === 'create'),
            TextInput::make('full_name')
                ->label(__('drivers.full_name'))
                ->required()
                ->maxLength(255),
            TextInput::make('full_name_ar')
                ->label(__('drivers.full_name_ar'))
                ->maxLength(255),
            TextInput::make('phone')
                ->label(__('drivers.phone'))
                ->tel()
                ->required()
                ->maxLength(255),
            TextInput::make('whatsapp_phone')
                ->label(__('drivers.whatsapp_phone'))
                ->tel()
                ->maxLength(255),
            Textarea::make('address')
                ->label(__('drivers.address'))
                ->rows(2),
            DatePicker::make('date_of_birth')
                ->label(__('drivers.date_of_birth'))
                ->maxDate(now()->subYears(18)),
            DatePicker::make('hire_date')
                ->label(__('drivers.hire_date')),
            Select::make('employment_type')
                ->label(__('drivers.employment_type'))
                ->options(EmploymentType::class)
                ->default(EmploymentType::Salaried->value)
                ->required(),
            TextInput::make('base_salary')
                ->label(__('drivers.base_salary'))
                ->numeric()
                ->prefix('EGP')
                ->default(0)
                ->required(),
            TextInput::make('trip_commission_percentage')
                ->label(__('drivers.trip_commission_percentage'))
                ->numeric()
                ->suffix('%')
                ->default(0)
                ->required(),
            Select::make('status')
                ->label(__('drivers.status'))
                ->options(DriverStatus::class)
                ->default(DriverStatus::Active->value)
                ->required(),
            TextInput::make('rating')
                ->label(__('drivers.rating'))
                ->numeric()
                ->minValue(0)
                ->maxValue(5)
                ->step(0.01),
            Textarea::make('notes')
                ->label(__('drivers.notes'))
                ->rows(3),
        ]);
    }
}
