<?php

declare(strict_types=1);

namespace App\Filament\Admin\Resources\Drivers\RelationManagers;

use App\Enums\DriverDocumentType;
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
                ->label(__('driver_documents.doc_type'))
                ->options(DriverDocumentType::class)
                ->required(),
            TextInput::make('document_number')
                ->label(__('driver_documents.document_number'))
                ->maxLength(255),
            DatePicker::make('issue_date')
                ->label(__('driver_documents.issue_date')),
            DatePicker::make('expiry_date')
                ->label(__('driver_documents.expiry_date')),
            TextInput::make('issuer')
                ->label(__('driver_documents.issuer'))
                ->maxLength(255),
            FileUpload::make('file_path')
                ->label(__('driver_documents.file'))
                ->disk('public')
                ->directory('driver-documents'),
            Toggle::make('is_active')
                ->label(__('driver_documents.is_active'))
                ->default(true),
        ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('document_number')
            ->columns([
                TextColumn::make('doc_type')
                    ->label(__('driver_documents.doc_type'))
                    ->badge(),
                TextColumn::make('document_number')
                    ->label(__('driver_documents.document_number'))
                    ->searchable(),
                TextColumn::make('issue_date')
                    ->label(__('driver_documents.issue_date'))
                    ->date(),
                TextColumn::make('expiry_date')
                    ->label(__('driver_documents.expiry_date'))
                    ->date()
                    ->color(fn ($state) => $state && now()->gt($state) ? 'danger' : null),
                TextColumn::make('issuer')
                    ->label(__('driver_documents.issuer'))
                    ->toggleable(),
                IconColumn::make('is_active')
                    ->label(__('driver_documents.is_active'))
                    ->boolean(),
            ])
            ->filters([
                SelectFilter::make('doc_type')->options(DriverDocumentType::class),
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
