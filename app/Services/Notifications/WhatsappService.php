<?php

declare(strict_types=1);

namespace App\Services\Notifications;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use RuntimeException;

/**
 * Green API WhatsApp client. Endpoint shape (per Green API docs):
 *
 *   POST {base}/waInstance{instanceId}/sendMessage/{token}
 *   { chatId: "201234567890@c.us", message: "..." }
 *
 * Returns the Green API message id on success, null on transport failure
 * (logged, never thrown to the queue — a failed WhatsApp send shouldn't
 * roll back the business event that triggered it).
 *
 * Env config (filled in production .env, empty in dev/test):
 *   GREEN_API_BASE_URL, GREEN_API_INSTANCE_ID, GREEN_API_TOKEN
 *
 * In dev/test, when instance_id/token are empty the service short-circuits:
 *   - logs the would-be send at info level
 *   - returns a fake message id so callers don't branch on null
 *   - never hits the network
 *
 * Mock Green API in tests with Http::fake() against waInstance*\/sendMessage.
 */
class WhatsappService
{
    public function __construct(
        private readonly ?string $baseUrl,
        private readonly ?string $instanceId,
        private readonly ?string $token,
    ) {}

    public static function fromConfig(): self
    {
        return new self(
            baseUrl: rtrim((string) config('services.green_api.base_url', 'https://api.green-api.com'), '/'),
            instanceId: config('services.green_api.instance_id') ?: null,
            token: config('services.green_api.token') ?: null,
        );
    }

    /**
     * Send a plain-text message to a phone number. Phone gets normalized to
     * digits-only and the @c.us suffix is added. Leading + and spaces stripped.
     */
    public function send(string $phone, string $message): ?string
    {
        $normalized = preg_replace('/\D+/', '', $phone) ?? '';
        if ($normalized === '') {
            throw new RuntimeException('Cannot send WhatsApp to empty phone number.');
        }

        if (! $this->isConfigured()) {
            Log::info('WhatsApp (unconfigured) would send', [
                'phone' => $normalized,
                'preview' => mb_substr($message, 0, 120),
            ]);

            return 'fake-id-'.bin2hex(random_bytes(6));
        }

        $url = sprintf(
            '%s/waInstance%s/sendMessage/%s',
            $this->baseUrl,
            $this->instanceId,
            $this->token,
        );

        try {
            $response = Http::timeout(15)->post($url, [
                'chatId' => $normalized.'@c.us',
                'message' => $message,
            ]);

            if (! $response->successful()) {
                Log::warning('WhatsApp send failed', [
                    'status' => $response->status(),
                    'body' => mb_substr((string) $response->body(), 0, 500),
                    'phone' => $normalized,
                ]);

                return null;
            }

            return $response->json('idMessage');
        } catch (\Throwable $e) {
            Log::warning('WhatsApp transport error', [
                'error' => $e->getMessage(),
                'phone' => $normalized,
            ]);

            return null;
        }
    }

    public function isConfigured(): bool
    {
        return $this->instanceId !== null && $this->token !== null;
    }
}
