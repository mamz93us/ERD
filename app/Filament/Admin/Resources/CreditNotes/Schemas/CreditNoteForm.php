<?php

declare(strict_types=1);

namespace App\Filament\Admin\Resources\CreditNotes\Schemas;

use App\Enums\CreditNoteReason;
use App\Enums\CreditNoteStatus;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class CreditNoteForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema->components([
            Placeholder::make('note_number')
                ->label(__('credit_notes.note_number'))
                ->content(fn ($record) => $record?->note_number ?? __('credit_notes.auto_generated_on_save')),
            Select::make('status')
                ->label(__('credit_notes.status'))
                ->options(CreditNoteStatus::class)
                ->disabled()
                ->dehydrated(false),
            Select::make('invoice_id')
                ->label(__('credit_notes.invoice'))
                ->relationship('invoice', 'invoice_number')
                ->searchable()
                ->preload()
                ->required(),
            Select::make('reason')
                ->label(__('credit_notes.reason'))
                ->options(CreditNoteReason::class)
                ->required(),
            DatePicker::make('issue_date')
                ->label(__('credit_notes.issue_date'))
                ->required()
                ->default(now()),
            TextInput::make('amount')
                ->label(__('credit_notes.amount'))
                ->numeric()
                ->prefix('EGP')
                ->required()
                ->helperText(__('credit_notes.amount_help')),
            Textarea::make('reason_details')
                ->label(__('credit_notes.reason_details'))
                ->required()
                ->rows(3)
                ->columnSpanFull(),
            Select::make('created_by_user_id')
                ->label(__('credit_notes.created_by'))
                ->relationship('createdBy', 'full_name')
                ->default(auth()->id())
                ->required(),
            Select::make('approved_by_user_id')
                ->label(__('credit_notes.approved_by'))
                ->relationship('approvedBy', 'full_name')
                ->disabled()
                ->dehydrated(false),
        ]);
    }
}
