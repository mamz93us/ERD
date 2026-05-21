<?php

declare(strict_types=1);

namespace App\Observers;

use App\Models\Quotation;

class QuotationNumberObserver
{
    public function creating(Quotation $quotation): void
    {
        if (empty($quotation->quotation_number)) {
            $quotation->quotation_number = Quotation::nextNumber();
        }
    }
}
