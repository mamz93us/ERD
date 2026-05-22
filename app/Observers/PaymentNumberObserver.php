<?php

declare(strict_types=1);

namespace App\Observers;

use App\Models\Payment;

class PaymentNumberObserver
{
    public function creating(Payment $payment): void
    {
        if (empty($payment->payment_number)) {
            $payment->payment_number = Payment::nextNumber();
        }
    }
}
