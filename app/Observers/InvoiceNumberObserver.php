<?php

declare(strict_types=1);

namespace App\Observers;

use App\Models\Invoice;

class InvoiceNumberObserver
{
    public function creating(Invoice $invoice): void
    {
        if (empty($invoice->invoice_number)) {
            $invoice->invoice_number = Invoice::nextNumber();
        }
    }
}
