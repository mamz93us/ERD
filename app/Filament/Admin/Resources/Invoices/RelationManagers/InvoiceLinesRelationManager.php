<?php

declare(strict_types=1);

namespace App\Filament\Admin\Resources\Invoices\RelationManagers;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\CreateAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\TextInput;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\Summarizers\Sum;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class InvoiceLinesRelationManager extends RelationManager
{
    protected static string $relationship = 'lines';

    public function form(Schema $schema): Schema
    {
        return $schema->components([
            TextInput::make('description')
                ->label(__('invoice_lines.description'))
                ->required()
                ->maxLength(255)
                ->columnSpanFull(),
            TextInput::make('quantity')
                ->label(__('invoice_lines.quantity'))
                ->numeric()
                ->default(1)
                ->required(),
            TextInput::make('unit_price')
                ->label(__('invoice_lines.unit_price'))
                ->numeric()
                ->prefix('EGP')
                ->required(),
            TextInput::make('discount_amount')
                ->label(__('invoice_lines.discount_amount'))
                ->numeric()
                ->prefix('EGP')
                ->default(0),
            TextInput::make('vat_rate')
                ->label(__('invoice_lines.vat_rate'))
                ->numeric()
                ->suffix('%')
                ->default(14)
                ->required(),
            TextInput::make('vat_amount')
                ->label(__('invoice_lines.vat_amount'))
                ->numeric()
                ->prefix('EGP')
                ->default(0)
                ->required(),
            TextInput::make('line_total')
                ->label(__('invoice_lines.line_total'))
                ->numeric()
                ->prefix('EGP')
                ->required(),
            TextInput::make('sort_order')
                ->label(__('invoice_lines.sort_order'))
                ->numeric()
                ->default(0),
        ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('description')
            ->defaultSort('sort_order')
            ->columns([
                TextColumn::make('description')
                    ->label(__('invoice_lines.description'))
                    ->wrap(),
                TextColumn::make('quantity')
                    ->label(__('invoice_lines.quantity'))
                    ->numeric(2),
                TextColumn::make('unit_price')
                    ->label(__('invoice_lines.unit_price'))
                    ->money('EGP'),
                TextColumn::make('vat_rate')
                    ->label(__('invoice_lines.vat_rate'))
                    ->suffix('%'),
                TextColumn::make('vat_amount')
                    ->label(__('invoice_lines.vat_amount'))
                    ->money('EGP'),
                TextColumn::make('line_total')
                    ->label(__('invoice_lines.line_total'))
                    ->money('EGP')
                    ->summarize(Sum::make()->money('EGP')),
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
