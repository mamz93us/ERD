<?php

declare(strict_types=1);

namespace App\Notifications;

use App\Models\CarDocument;
use App\Models\DriverDocument;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Notification;

class DocumentExpired extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(public readonly CarDocument|DriverDocument $document) {}

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
            'expiry_date' => $this->document->expiry_date?->toDateString(),
        ];
    }
}
