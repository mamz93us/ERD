<?php

declare(strict_types=1);

namespace App\Notifications;

use App\Models\Invoice;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Notifications\Messages\MailMessage;

class InvoiceIssued extends TemplatedNotification
{
    protected string $templateKey = 'invoice_issued';

    public function __construct(public readonly Invoice $invoice) {}

    protected function vars(object $notifiable): array
    {
        return [
            'customer_name' => $notifiable->full_name ?? '',
            'invoice_number' => $this->invoice->invoice_number,
            'total' => number_format((float) $this->invoice->total, 2).' EGP',
            'due_date' => $this->invoice->due_date?->format('Y-m-d'),
            'balance_due' => number_format((float) $this->invoice->balance_due, 2).' EGP',
        ];
    }

    /**
     * Attach the bilingual PDF to the email per spec §6 Phase 9
     * "InvoiceIssued (to customer with PDF)".
     */
    public function toMail(object $notifiable): MailMessage
    {
        $msg = parent::toMail($notifiable);

        $locale = $this->localeFor($notifiable);
        $pdf = Pdf::loadView('pdfs.invoice', [
            'invoice' => $this->invoice->load(['customer', 'corporateAccount', 'lines']),
            'locale' => $locale,
        ]);

        return $msg->attachData($pdf->output(), "{$this->invoice->invoice_number}.pdf", [
            'mime' => 'application/pdf',
        ]);
    }

    protected function databasePayload(): array
    {
        return [
            'invoice_id' => $this->invoice->id,
            'invoice_number' => $this->invoice->invoice_number,
            'total' => (string) $this->invoice->total,
        ];
    }
}
