<?php

declare(strict_types=1);

namespace App\Services\Compliance;

use App\Models\TrafficFine;
use App\Models\Trip;
use Carbon\CarbonImmutable;

/**
 * Spec §6 Phase 7: "TrafficFineAttributionService finds the trip active for
 * that car at the violation timestamp and sets trip_id + driver_id."
 *
 * Called by TrafficFineObserver::creating when trip_id is null. If a trip
 * already has trip_id (manual override during data entry), the service is a
 * no-op so the operator's choice wins.
 *
 * "Active for that car at violation_date" means a trip where:
 *  - car_id = fine's car
 *  - scheduled_start <= violation_date <= scheduled_end
 *  - status is NOT one of (cancelled, no_show)
 *
 * Picks the first match by scheduled_start desc when more than one (rare:
 * back-to-back trips on the same car covering the boundary — earlier trip
 * wins because we sort by start desc, NOT asc; flipped intentionally so
 * a trip that started at 17:00 right after a 09:00 trip ending at 17:00
 * gets attributed if the timestamp is exactly 17:00).
 */
class TrafficFineAttributionService
{
    public function attribute(TrafficFine $fine): void
    {
        if ($fine->trip_id !== null || $fine->violation_date === null || $fine->car_id === null) {
            return;
        }

        $violationAt = CarbonImmutable::parse($fine->violation_date);

        $trip = Trip::query()
            ->withoutGlobalScopes()
            ->where('car_id', $fine->car_id)
            ->whereNotIn('status', ['cancelled', 'no_show'])
            ->where('scheduled_start', '<=', $violationAt)
            ->where('scheduled_end', '>=', $violationAt)
            ->orderByDesc('scheduled_start')
            ->first();

        if ($trip === null) {
            return;
        }

        $fine->trip_id = $trip->id;
        if ($fine->driver_id === null) {
            $fine->driver_id = $trip->driver_id;
        }
    }
}
