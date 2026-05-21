<?php

declare(strict_types=1);

namespace App\Enums;

use Filament\Support\Contracts\HasLabel;

enum EmploymentType: string implements HasLabel
{
    case Salaried = 'salaried';
    case Freelance = 'freelance';
    case OnDemand = 'on_demand';

    public function getLabel(): string
    {
        return __("enums.employment_type.{$this->value}");
    }
}
