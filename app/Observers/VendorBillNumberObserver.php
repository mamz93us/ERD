<?php

declare(strict_types=1);

namespace App\Observers;

use App\Models\VendorBill;

class VendorBillNumberObserver
{
    public function creating(VendorBill $bill): void
    {
        if (empty($bill->bill_number)) {
            $bill->bill_number = VendorBill::nextNumber();
        }
    }
}
