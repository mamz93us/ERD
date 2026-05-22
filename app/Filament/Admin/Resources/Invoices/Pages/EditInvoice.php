<?php

declare(strict_types=1);

namespace App\Filament\Admin\Resources\Invoices\Pages;

use App\Enums\InvoiceStatus;
use App\Filament\Admin\Resources\Invoices\InvoiceResource;
use App\Models\Invoice;
use Filament\Actions\Action;
use Filament\Actions\DeleteAction;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\EditRecord;

class EditInvoice extends EditRecord
{
    protected static string $resource = InvoiceResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Action::make('mark_sent')
                ->label(__('invoices.mark_sent'))
                ->icon('heroicon-o-paper-airplane')
                ->color('info')
                ->visible(fn (Invoice $record): bool => $record->status === InvoiceStatus::Draft)
                ->action(function (Invoice $record): void {
                    $record->update(['status' => InvoiceStatus::Sent]);
                    Notification::make()->title(__('invoices.marked_sent'))->success()->send();
                    $this->refreshFormData(['status']);
                }),
            DeleteAction::make(),
        ];
    }
}
