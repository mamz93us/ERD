<?php

declare(strict_types=1);

namespace App\Enums;

use Filament\Support\Contracts\HasLabel;

enum ExpenseCategory: string implements HasLabel
{
    case Fuel = 'fuel';
    case Maintenance = 'maintenance';
    case Salaries = 'salaries';
    case Insurance = 'insurance';
    case Fines = 'fines';
    case Office = 'office';
    case Utilities = 'utilities';
    case Marketing = 'marketing';
    case Depreciation = 'depreciation';
    case Other = 'other';

    public function getLabel(): string
    {
        return __("enums.expense_category.{$this->value}");
    }
}
