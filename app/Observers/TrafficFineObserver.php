<?php

declare(strict_types=1);

namespace App\Observers;

use App\Models\TrafficFine;
use App\Services\Compliance\TrafficFineAttributionService;

class TrafficFineObserver
{
    public function __construct(private readonly TrafficFineAttributionService $attribution) {}

    public function creating(TrafficFine $fine): void
    {
        $this->attribution->attribute($fine);
    }
}
