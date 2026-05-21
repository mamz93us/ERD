<?php

declare(strict_types=1);

namespace App\Enums;

use Filament\Support\Contracts\HasLabel;

enum CommunicationChannel: string implements HasLabel
{
    case Whatsapp = 'whatsapp';
    case Email = 'email';
    case Phone = 'phone';
    case InPerson = 'in_person';
    case Sms = 'sms';

    public function getLabel(): string
    {
        return __("enums.communication_channel.{$this->value}");
    }
}
