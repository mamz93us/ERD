<?php

declare(strict_types=1);

namespace App\Filament\Admin\Resources\CreditNotes\Tables;

use App\Enums\CreditNoteReason;
use App\Enums\CreditNoteStatus;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class CreditNotesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->defaultSort('issue_date', 'desc')
            ->columns([
                TextColumn::make('note_number')
                    ->label(__('credit_notes.note_number'))
                    ->searchable()
                    ->weight('bold'),
                TextColumn::make('invoice.invoice_number')
                    ->label(__('credit_notes.invoice'))
                    ->badge(),
                TextColumn::make('issue_date')
                    ->label(__('credit_notes.issue_date'))
                    ->date()
                    ->sortable(),
                TextColumn::make('reason')
                    ->label(__('credit_notes.reason'))
                    ->badge(),
                TextColumn::make('amount')
                    ->label(__('credit_notes.amount'))
                    ->money('EGP')
                    ->sortable(),
                TextColumn::make('status')
                    ->label(__('credit_notes.status'))
                    ->badge(),
                TextColumn::make('createdBy.full_name')
                    ->label(__('credit_notes.created_by'))
                    ->toggleable(),
                TextColumn::make('approvedBy.full_name')
                    ->label(__('credit_notes.approved_by'))
                    ->placeholder('—')
                    ->toggleable(),
            ])
            ->filters([
                SelectFilter::make('status')->options(CreditNoteStatus::class),
                SelectFilter::make('reason')->options(CreditNoteReason::class),
            ])
            ->recordActions([
                EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
