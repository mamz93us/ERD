<?php

declare(strict_types=1);

namespace App\Notifications;

use App\Models\Payment;

class PaymentReceived extends TemplatedNotification
{
    protected string $templateKey = 'payment_received';

    public function __construct(public readonly Payment $payment) {}

    protected function vars(object $notifiable): array
    {
        return [
            'customer_name' => $notifiable->full_name ?? '',
            'payment_number' => $this->payment->payment_number,
            'amount' => number_format((float) $this->payment->amount, 2).' EGP',
            'payment_date' => $this->payment->payment_date?->format('Y-m-d'),
            'method' => $this->payment->method?->getLabel() ?? '',
        ];
    }

    protected function databasePayload(): array
    {
        return [
            'payment_id' => $this->payment->id,
            'payment_number' => $this->payment->payment_number,
            'amount' => (string) $this->payment->amount,
        ];
    }
}
