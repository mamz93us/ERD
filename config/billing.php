<?php

declare(strict_types=1);

return [
    /*
     * VAT rate applied by PricingService and InvoiceService. Egyptian standard is 14%.
     * Stored as a decimal fraction (0.14 = 14%).
     */
    'vat_rate' => env('BILLING_VAT_RATE', 0.14),

    /*
     * Default currency code. EGP locked per spec.
     */
    'currency' => env('BILLING_CURRENCY', 'EGP'),
];
