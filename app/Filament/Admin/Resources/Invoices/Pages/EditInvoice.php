<?php

declare(strict_types=1);

namespace App\Filament\Admin\Resources\Invoices\Pages;

use App\Enums\InvoiceStatus;
use App\Filament\Admin\Resources\Invoices\InvoiceResource;
use App\Models\Invoice;
use Barryvdh\DomPDF\Facade\Pdf;
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
            Action::make('download_pdf')
                ->label(__('invoices.download_pdf'))
                ->icon('heroicon-o-document-arrow-down')
                ->color('gray')
                ->action(function (Invoice $record) {
                    $locale = $record->customer?->preferred_language?->value ?? app()->getLocale();
                    $pdf = Pdf::loadView('pdfs.invoice', [
                        'invoice' => $record->load(['customer', 'corporateAccount', 'lines']),
                        'locale' => $locale,
                    ]);

                    return response()->streamDownload(
                        fn () => print ($pdf->output()),
                        "{$record->invoice_number}.pdf",
                        ['Content-Type' => 'application/pdf'],
                    );
                }),
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
