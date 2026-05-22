<?php

declare(strict_types=1);

namespace App\Enums;

use Filament\Support\Contracts\HasLabel;

enum PaymentMethod: string implements HasLabel
{
    case Cash = 'cash';
    case BankTransfer = 'bank_transfer';
    case Visa = 'visa';
    case Mastercard = 'mastercard';
    case Fawry = 'fawry';
    case Instapay = 'instapay';
    case Cheque = 'cheque';

    public function getLabel(): string
    {
        return __("enums.payment_method.{$this->value}");
    }
}
