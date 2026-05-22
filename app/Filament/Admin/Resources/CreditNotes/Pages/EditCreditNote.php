<?php

declare(strict_types=1);

namespace App\Filament\Admin\Resources\CreditNotes\Pages;

use App\Enums\CreditNoteStatus;
use App\Filament\Admin\Resources\CreditNotes\CreditNoteResource;
use App\Models\CreditNote;
use App\Services\Invoicing\CreditNoteService;
use Filament\Actions\Action;
use Filament\Actions\DeleteAction;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\EditRecord;

class EditCreditNote extends EditRecord
{
    protected static string $resource = CreditNoteResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Action::make('approve')
                ->label(__('credit_notes.approve'))
                ->icon('heroicon-o-check-circle')
                ->color('success')
                ->requiresConfirmation()
                ->visible(fn (CreditNote $record): bool => $record->status === CreditNoteStatus::PendingApproval)
                ->action(function (CreditNote $record): void {
                    try {
                        app(CreditNoteService::class)->approve($record, auth()->user());
                        Notification::make()->title(__('credit_notes.approved'))->success()->send();
                        $this->refreshFormData(['status', 'approved_by_user_id']);
                    } catch (\Throwable $e) {
                        Notification::make()->title($e->getMessage())->danger()->send();
                    }
                }),
            Action::make('reject')
                ->label(__('credit_notes.reject'))
                ->icon('heroicon-o-x-circle')
                ->color('danger')
                ->requiresConfirmation()
                ->visible(fn (CreditNote $record): bool => $record->status === CreditNoteStatus::PendingApproval)
                ->action(function (CreditNote $record): void {
                    app(CreditNoteService::class)->reject($record, auth()->user());
                    Notification::make()->title(__('credit_notes.rejected'))->success()->send();
                    $this->refreshFormData(['status']);
                }),
            Action::make('apply')
                ->label(__('credit_notes.apply'))
                ->icon('heroicon-o-arrow-down-on-square')
                ->color('info')
                ->requiresConfirmation()
                ->modalDescription(__('credit_notes.apply_confirm'))
                ->visible(fn (CreditNote $record): bool => $record->status === CreditNoteStatus::Approved)
                ->action(function (CreditNote $record): void {
                    try {
                        app(CreditNoteService::class)->apply($record);
                        Notification::make()->title(__('credit_notes.applied'))->success()->send();
                        $this->refreshFormData(['status']);
                    } catch (\Throwable $e) {
                        Notification::make()->title($e->getMessage())->danger()->send();
                    }
                }),
            DeleteAction::make(),
        ];
    }
}
