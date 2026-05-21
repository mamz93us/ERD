<?php

declare(strict_types=1);

namespace App\Filament\Admin\Resources\Trips\RelationManagers;

use App\Enums\TripDamageReportStatus;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\CreateAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class DamageReportsRelationManager extends RelationManager
{
    protected static string $relationship = 'damageReports';

    public function form(Schema $schema): Schema
    {
        return $schema->components([
            Textarea::make('description')
                ->label(__('trip_damage_reports.description'))
                ->required()
                ->rows(3)
                ->columnSpanFull(),
            FileUpload::make('photos')
                ->label(__('trip_damage_reports.photos'))
                ->multiple()
                ->disk('public')
                ->directory('damage-photos')
                ->columnSpanFull(),
            TextInput::make('repair_cost_estimate')
                ->label(__('trip_damage_reports.repair_cost_estimate'))
                ->numeric()
                ->prefix('EGP')
                ->default(0)
                ->required(),
            TextInput::make('actual_repair_cost')
                ->label(__('trip_damage_reports.actual_repair_cost'))
                ->numeric()
                ->prefix('EGP'),
            Toggle::make('charged_to_customer')
                ->label(__('trip_damage_reports.charged_to_customer'))
                ->live()
                ->default(false),
            TextInput::make('customer_charge_amount')
                ->label(__('trip_damage_reports.customer_charge_amount'))
                ->numeric()
                ->prefix('EGP')
                ->visible(fn ($get) => (bool) $get('charged_to_customer')),
            Select::make('status')
                ->label(__('trip_damage_reports.status'))
                ->options(TripDamageReportStatus::class)
                ->default(TripDamageReportStatus::Reported->value)
                ->required(),
        ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('description')
            ->defaultSort('created_at', 'desc')
            ->columns([
                TextColumn::make('description')
                    ->label(__('trip_damage_reports.description'))
                    ->limit(50)
                    ->wrap(),
                TextColumn::make('status')
                    ->label(__('trip_damage_reports.status'))
                    ->badge(),
                TextColumn::make('repair_cost_estimate')
                    ->label(__('trip_damage_reports.repair_cost_estimate'))
                    ->money('EGP'),
                TextColumn::make('actual_repair_cost')
                    ->label(__('trip_damage_reports.actual_repair_cost'))
                    ->money('EGP')
                    ->placeholder('—'),
                IconColumn::make('charged_to_customer')
                    ->label(__('trip_damage_reports.charged_to_customer'))
                    ->boolean(),
            ])
            ->filters([
                SelectFilter::make('status')->options(TripDamageReportStatus::class),
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
