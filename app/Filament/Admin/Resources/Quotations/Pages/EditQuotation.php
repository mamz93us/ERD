<?php

declare(strict_types=1);

namespace App\Filament\Admin\Resources\Quotations\Pages;

use App\Filament\Admin\Resources\Quotations\QuotationResource;
use App\Models\Quotation;
use Barryvdh\DomPDF\Facade\Pdf;
use Filament\Actions\Action;
use Filament\Actions\DeleteAction;
use Filament\Actions\ForceDeleteAction;
use Filament\Actions\RestoreAction;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\EditRecord;

class EditQuotation extends EditRecord
{
    protected static string $resource = QuotationResource::class;

    /**
     * @param  array<string,mixed>  $data
     * @return array<string,mixed>
     */
    protected function mutateFormDataBeforeSave(array $data): array
    {
        return QuotationResource::applyPricing($data);
    }

    protected function getHeaderActions(): array
    {
        return [
            Action::make('download_pdf')
                ->label(__('quotations.download_pdf'))
                ->icon('heroicon-o-document-arrow-down')
                ->color('gray')
                ->action(function (Quotation $record) {
                    $locale = $record->customer?->preferred_language?->value ?? app()->getLocale();
                    $pdf = Pdf::loadView('pdfs.quotation', [
                        'quotation' => $record->load(['customer', 'corporateAccount', 'category', 'rateCard']),
                        'locale' => $locale,
                    ]);

                    return response()->streamDownload(
                        fn () => print ($pdf->output()),
                        "{$record->quotation_number}.pdf",
                        ['Content-Type' => 'application/pdf'],
                    );
                }),
            Action::make('send')
                ->label(__('quotations.send'))
                ->icon('heroicon-o-paper-airplane')
                ->color('primary')
                ->requiresConfirmation()
                ->modalDescription(__('quotations.send_stub_notice'))
                ->visible(fn (Quotation $record) => filled($record->customer?->whatsapp_phone) || filled($record->customer?->email))
                ->action(function (Quotation $record): void {
                    // Phase 9 will replace this stub with Green API WhatsApp + Amazon SES with PDF attachment.
                    // For Phase 4, we just mark the quotation as sent and surface the WhatsApp deep-link.
                    $record->update(['status' => 'sent']);
                    Notification::make()
                        ->title(__('quotations.send_stub_title'))
                        ->body($record->customer?->whatsapp_phone
                            ? 'wa.me/'.preg_replace('/\D/', '', (string) $record->customer->whatsapp_phone)
                            : __('quotations.send_no_channel'))
                        ->success()
                        ->send();
                }),
            DeleteAction::make(),
            ForceDeleteAction::make(),
            RestoreAction::make(),
        ];
    }
}
