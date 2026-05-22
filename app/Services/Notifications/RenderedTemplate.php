<?php

declare(strict_types=1);

namespace App\Services\Notifications;

final class RenderedTemplate
{
    public function __construct(
        public readonly ?string $subject,
        public readonly string $body,
        public readonly bool $templateFound,
    ) {}
}
