<?php

declare(strict_types=1);

namespace App\Notifications;

use App\Services\Notifications\TemplateRenderer;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

/**
 * Shared scaffolding for all 7 Phase 9 notifications.
 *
 * Subclasses declare:
 *   - protected string $templateKey  e.g. 'booking_confirmed'
 *   - protected function vars(object $notifiable): array  the placeholder bag
 *   - (optional) protected function databasePayload(): array
 *   - (optional) protected function channelsFor(object $notifiable): array
 *
 * Channel routing logic: every notification goes to `database` (admin
 * bell menu). WhatsApp added when the notifiable has a phone routable.
 * Mail added when they have an email. Override channelsFor() if a
 * specific notification needs to restrict channels.
 */
abstract class TemplatedNotification extends Notification implements ShouldQueue
{
    use Queueable;

    protected string $templateKey = '';

    /** @return array<string,scalar|null> */
    abstract protected function vars(object $notifiable): array;

    /** @return array<string,mixed> */
    protected function databasePayload(): array
    {
        return [];
    }

    /** @return list<string> */
    protected function channelsFor(object $notifiable): array
    {
        $channels = ['database'];

        if (method_exists($notifiable, 'routeNotificationForWhatsapp') && $notifiable->routeNotificationForWhatsapp()) {
            $channels[] = 'whatsapp';
        }
        if (! empty($notifiable->email ?? null)) {
            $channels[] = 'mail';
        }

        return $channels;
    }

    /** @return list<string> */
    public function via(object $notifiable): array
    {
        return $this->channelsFor($notifiable);
    }

    public function toWhatsapp(object $notifiable): string
    {
        return app(TemplateRenderer::class)
            ->render($this->templateKey, 'whatsapp', $this->localeFor($notifiable), $this->vars($notifiable))
            ->body;
    }

    public function toMail(object $notifiable): MailMessage
    {
        $rendered = app(TemplateRenderer::class)
            ->render($this->templateKey, 'mail', $this->localeFor($notifiable), $this->vars($notifiable));

        $msg = (new MailMessage)->line($rendered->body);
        if ($rendered->subject !== null) {
            $msg->subject($rendered->subject);
        }

        return $msg;
    }

    /** @return array<string,mixed> */
    public function toArray(object $notifiable): array
    {
        return array_merge(
            ['template_key' => $this->templateKey],
            $this->databasePayload(),
        );
    }

    protected function localeFor(object $notifiable): string
    {
        return method_exists($notifiable, 'preferredLocale')
            ? $notifiable->preferredLocale()
            : config('app.locale', 'ar');
    }
}
