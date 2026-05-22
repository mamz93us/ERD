<?php

declare(strict_types=1);

namespace App\Notifications\Channels;

use App\Services\Notifications\WhatsappService;
use Illuminate\Notifications\Notification;

/**
 * Adapter glue between Laravel's notification system and WhatsappService.
 *
 * A notification opts in by listing `'whatsapp'` in its via() AND implementing
 * `toWhatsapp(object $notifiable): string|array` that returns either the body
 * text or `['to' => '+201...', 'body' => '...']` if the caller wants to
 * override the route.
 */
class WhatsappChannel
{
    public function __construct(private readonly WhatsappService $whatsapp) {}

    public function send(object $notifiable, Notification $notification): ?string
    {
        if (! method_exists($notification, 'toWhatsapp')) {
            return null;
        }

        $payload = $notification->toWhatsapp($notifiable);

        if (is_array($payload)) {
            $to = $payload['to'] ?? null;
            $body = (string) ($payload['body'] ?? '');
        } else {
            $to = method_exists($notifiable, 'routeNotificationForWhatsapp')
                ? $notifiable->routeNotificationForWhatsapp()
                : ($notifiable->whatsapp_phone ?? $notifiable->phone ?? null);
            $body = (string) $payload;
        }

        if ($to === null || $to === '' || $body === '') {
            return null;
        }

        return $this->whatsapp->send($to, $body);
    }
}
