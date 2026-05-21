<?php

declare(strict_types=1);

namespace App\Filament\Admin\Resources\Cars\RelationManagers;

use App\Enums\SubRentalContractStatus;
use App\Models\PartnerAgency;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\CreateAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class SubRentalContractsRelationManager extends RelationManager
{
    protected static string $relationship = 'subRentalContracts';

    public function form(Schema $schema): Schema
    {
        return $schema->components([
            Select::make('partner_agency_id')
                ->label(__('sub_rental_contracts.partner_agency'))
                ->options(fn () => PartnerAgency::query()->pluck('name', 'id'))
                ->searchable()
                ->required(),
            DatePicker::make('start_date')
                ->label(__('sub_rental_contracts.start_date'))
                ->required(),
            DatePicker::make('end_date')
                ->label(__('sub_rental_contracts.end_date'))
                ->required()
                ->afterOrEqual('start_date'),
            TextInput::make('daily_cost')
                ->label(__('sub_rental_contracts.daily_cost'))
                ->numeric()
                ->prefix('EGP')
                ->required(),
            TextInput::make('included_km_per_day')
                ->label(__('sub_rental_contracts.included_km_per_day'))
                ->numeric()
                ->suffix(' km'),
            TextInput::make('extra_km_cost')
                ->label(__('sub_rental_contracts.extra_km_cost'))
                ->numeric()
                ->prefix('EGP'),
            Textarea::make('terms')
                ->label(__('sub_rental_contracts.terms'))
                ->rows(3),
            Select::make('status')
                ->label(__('sub_rental_contracts.status'))
                ->options(SubRentalContractStatus::class)
                ->default(SubRentalContractStatus::Active->value)
                ->required(),
            FileUpload::make('contract_file_path')
                ->label(__('sub_rental_contracts.contract_file'))
                ->disk('public')
                ->directory('sub-rental-contracts'),
        ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('start_date')
            ->defaultSort('end_date', 'desc')
            ->columns([
                TextColumn::make('partnerAgency.name')
                    ->label(__('sub_rental_contracts.partner_agency'))
                    ->searchable(),
                TextColumn::make('start_date')
                    ->label(__('sub_rental_contracts.start_date'))
                    ->date(),
                TextColumn::make('end_date')
                    ->label(__('sub_rental_contracts.end_date'))
                    ->date()
                    ->color(fn ($state) => $state && now()->gt($state) ? 'danger' : null),
                TextColumn::make('daily_cost')
                    ->label(__('sub_rental_contracts.daily_cost'))
                    ->money('EGP'),
                TextColumn::make('status')
                    ->label(__('sub_rental_contracts.status'))
                    ->badge(),
            ])
            ->filters([
                SelectFilter::make('status')->options(SubRentalContractStatus::class),
            ])
            ->headerActions([
                CreateAction::make(),
            ])
            ->recordActions([
                EditAction::make(),
                DeleteAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
