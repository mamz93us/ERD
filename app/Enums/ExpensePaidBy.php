<?php

declare(strict_types=1);

namespace App\Enums;

use Filament\Support\Contracts\HasLabel;

enum ExpensePaidBy: string implements HasLabel
{
    case Cash = 'cash';
    case Bank = 'bank';
    case PettyCash = 'petty_cash';

    public function getLabel(): string
    {
        return __("enums.expense_paid_by.{$this->value}");
    }
}
