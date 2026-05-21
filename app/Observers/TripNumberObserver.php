<?php

declare(strict_types=1);

namespace App\Observers;

use App\Models\Trip;

class TripNumberObserver
{
    public function creating(Trip $trip): void
    {
        if (empty($trip->trip_number)) {
            $trip->trip_number = Trip::nextNumber();
        }
    }
}
