<?php

declare(strict_types=1);

namespace App\Notifications;

use App\Models\CarDocument;
use App\Models\DriverDocument;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

/**
 * Phase 3: sent via database channel only (so admin sees it in their bell menu).
 * Phase 9 will add WhatsApp (Green API) and email channels here.
 */
class DocumentExpiringSoon extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(
        public readonly CarDocument|DriverDocument $document,
        public readonly int $daysUntilExpiry,
    ) {}

    /** @return list<string> */
    public function via(object $notifiable): array
    {
        return ['database'];
    }

    /** @return array<string,mixed> */
    public function toArray(object $notifiable): array
    {
        return [
            'document_model' => $this->document::class,
            'document_id' => $this->document->id,
            'doc_type' => (string) $this->document->doc_type?->value,
            'days_until_expiry' => $this->daysUntilExpiry,
            'expiry_date' => $this->document->expiry_date?->toDateString(),
            'subject' => $this->subjectIdentifier(),
        ];
    }

    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject(__('notifications.document_expiring_soon.subject'))
            ->line(__('notifications.document_expiring_soon.body', [
                'subject' => $this->subjectIdentifier(),
                'days' => $this->daysUntilExpiry,
                'date' => $this->document->expiry_date?->toDateString() ?? '—',
            ]));
    }

    private function subjectIdentifier(): string
    {
        if ($this->document instanceof CarDocument) {
            return $this->document->car?->plate ?? 'car';
        }

        return $this->document->driver?->full_name ?? 'driver';
    }
}
