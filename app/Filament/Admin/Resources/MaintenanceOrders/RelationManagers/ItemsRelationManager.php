<?php

declare(strict_types=1);

namespace App\Filament\Admin\Resources\MaintenanceOrders\RelationManagers;

use App\Enums\MaintenanceItemType;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\CreateAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\Summarizers\Sum;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class ItemsRelationManager extends RelationManager
{
    protected static string $relationship = 'items';

    public function form(Schema $schema): Schema
    {
        return $schema->components([
            Select::make('item_type')
                ->label(__('maintenance_items.item_type'))
                ->options(MaintenanceItemType::class)
                ->required(),
            TextInput::make('description')
                ->label(__('maintenance_items.description'))
                ->required()
                ->maxLength(255),
            TextInput::make('quantity')
                ->label(__('maintenance_items.quantity'))
                ->numeric()
                ->default(1)
                ->required(),
            TextInput::make('unit_cost')
                ->label(__('maintenance_items.unit_cost'))
                ->numeric()
                ->prefix('EGP')
                ->required(),
            TextInput::make('total_cost')
                ->label(__('maintenance_items.total_cost'))
                ->numeric()
                ->prefix('EGP')
                ->readOnly()
                ->helperText(__('maintenance_items.total_cost_help')),
        ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('description')
            ->columns([
                TextColumn::make('item_type')
                    ->label(__('maintenance_items.item_type'))
                    ->badge(),
                TextColumn::make('description')
                    ->label(__('maintenance_items.description'))
                    ->searchable(),
                TextColumn::make('quantity')
                    ->label(__('maintenance_items.quantity'))
                    ->numeric(2),
                TextColumn::make('unit_cost')
                    ->label(__('maintenance_items.unit_cost'))
                    ->money('EGP'),
                TextColumn::make('total_cost')
                    ->label(__('maintenance_items.total_cost'))
                    ->money('EGP')
                    ->summarize(Sum::make()->money('EGP')),
            ])
            ->filters([
                SelectFilter::make('item_type')->options(MaintenanceItemType::class),
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
