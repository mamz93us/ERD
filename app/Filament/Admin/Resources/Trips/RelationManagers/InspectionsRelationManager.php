<?php

declare(strict_types=1);

namespace App\Filament\Admin\Resources\Trips\RelationManagers;

use App\Enums\FuelLevel;
use App\Enums\TripInspectionStage;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\CreateAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\KeyValue;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class InspectionsRelationManager extends RelationManager
{
    protected static string $relationship = 'inspections';

    public function form(Schema $schema): Schema
    {
        return $schema->components([
            Select::make('stage')
                ->label(__('trip_inspections.stage'))
                ->options(TripInspectionStage::class)
                ->required(),
            TextInput::make('odometer')
                ->label(__('trip_inspections.odometer'))
                ->numeric()
                ->suffix('km')
                ->required(),
            Select::make('fuel_level')
                ->label(__('trip_inspections.fuel_level'))
                ->options(FuelLevel::class)
                ->required(),
            DateTimePicker::make('performed_at')
                ->label(__('trip_inspections.performed_at'))
                ->default(now())
                ->required()
                ->seconds(false),
            KeyValue::make('accessories_checklist')
                ->label(__('trip_inspections.accessories_checklist'))
                ->keyLabel(__('trip_inspections.accessory'))
                ->valueLabel(__('trip_inspections.present'))
                ->reorderable(),
            FileUpload::make('driver_signature_path')
                ->label(__('trip_inspections.driver_signature'))
                ->disk('public')
                ->directory('signatures')
                ->required(),
            FileUpload::make('customer_signature_path')
                ->label(__('trip_inspections.customer_signature'))
                ->disk('public')
                ->directory('signatures'),
            FileUpload::make('photos')
                ->label(__('trip_inspections.photos'))
                ->multiple()
                ->disk('public')
                ->directory('inspection-photos'),
            Textarea::make('notes')
                ->label(__('trip_inspections.notes'))
                ->rows(3)
                ->columnSpanFull(),
        ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('stage')
            ->defaultSort('performed_at')
            ->columns([
                TextColumn::make('stage')
                    ->label(__('trip_inspections.stage'))
                    ->badge(),
                TextColumn::make('performed_at')
                    ->label(__('trip_inspections.performed_at'))
                    ->dateTime(),
                TextColumn::make('odometer')
                    ->label(__('trip_inspections.odometer'))
                    ->suffix(' km'),
                TextColumn::make('fuel_level')
                    ->label(__('trip_inspections.fuel_level'))
                    ->badge(),
                TextColumn::make('inspector.full_name')
                    ->label(__('trip_inspections.inspector'))
                    ->toggleable(),
            ])
            ->filters([
                SelectFilter::make('stage')->options(TripInspectionStage::class),
            ])
            ->headerActions([
                CreateAction::make()
                    ->mutateDataUsing(function (array $data): array {
                        $data['inspector_user_id'] = auth()->id();

                        return $data;
                    }),
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
