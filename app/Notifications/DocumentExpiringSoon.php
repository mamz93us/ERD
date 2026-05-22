<?php

declare(strict_types=1);

namespace App\Notifications;

use App\Models\CarDocument;
use App\Models\DriverDocument;

/**
 * Phase 3 origin: database channel only (admin bell menu). Phase 9 extends
 * to WhatsApp + email via TemplatedNotification.
 */
class DocumentExpiringSoon extends TemplatedNotification
{
    protected string $templateKey = 'document_expiring_soon';

    public function __construct(
        public readonly CarDocument|DriverDocument $document,
        public readonly int $daysUntilExpiry,
    ) {}

    protected function vars(object $notifiable): array
    {
        return [
            'recipient_name' => $notifiable->full_name ?? '',
            'subject' => $this->subjectIdentifier(),
            'doc_type' => (string) $this->document->doc_type?->value,
            'days' => $this->daysUntilExpiry,
            'expiry_date' => $this->document->expiry_date?->toDateString() ?? '—',
        ];
    }

    protected function databasePayload(): array
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

    private function subjectIdentifier(): string
    {
        if ($this->document instanceof CarDocument) {
            return $this->document->car?->plate ?? 'car';
        }

        return $this->document->driver?->full_name ?? 'driver';
    }
}
