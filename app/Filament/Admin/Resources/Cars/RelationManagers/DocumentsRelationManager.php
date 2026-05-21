<?php

declare(strict_types=1);

namespace App\Filament\Admin\Resources\Cars\RelationManagers;

use App\Enums\CarDocumentType;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\CreateAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Table;

class DocumentsRelationManager extends RelationManager
{
    protected static string $relationship = 'documents';

    public function form(Schema $schema): Schema
    {
        return $schema->components([
            Select::make('doc_type')
                ->label(__('car_documents.doc_type'))
                ->options(CarDocumentType::class)
                ->required(),
            TextInput::make('document_number')
                ->label(__('car_documents.document_number'))
                ->maxLength(255),
            DatePicker::make('issue_date')
                ->label(__('car_documents.issue_date')),
            DatePicker::make('expiry_date')
                ->label(__('car_documents.expiry_date')),
            TextInput::make('issuer')
                ->label(__('car_documents.issuer'))
                ->maxLength(255),
            TextInput::make('cost')
                ->label(__('car_documents.cost'))
                ->numeric()
                ->prefix('EGP'),
            FileUpload::make('file_path')
                ->label(__('car_documents.file'))
                ->disk('public')
                ->directory('car-documents'),
            Toggle::make('is_active')
                ->label(__('car_documents.is_active'))
                ->default(true)
                ->helperText(__('car_documents.is_active_help')),
        ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('document_number')
            ->defaultSort('expiry_date')
            ->columns([
                TextColumn::make('doc_type')
                    ->label(__('car_documents.doc_type'))
                    ->badge(),
                TextColumn::make('document_number')
                    ->label(__('car_documents.document_number'))
                    ->searchable(),
                TextColumn::make('issue_date')
                    ->label(__('car_documents.issue_date'))
                    ->date(),
                TextColumn::make('expiry_date')
                    ->label(__('car_documents.expiry_date'))
                    ->date()
                    ->color(fn ($state) => $state && now()->gt($state) ? 'danger' : null),
                TextColumn::make('issuer')
                    ->label(__('car_documents.issuer'))
                    ->toggleable(),
                TextColumn::make('cost')
                    ->label(__('car_documents.cost'))
                    ->money('EGP')
                    ->toggleable(),
                IconColumn::make('is_active')
                    ->label(__('car_documents.is_active'))
                    ->boolean(),
            ])
            ->filters([
                SelectFilter::make('doc_type')->options(CarDocumentType::class),
                TernaryFilter::make('is_active'),
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
