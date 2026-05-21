<?php

declare(strict_types=1);

namespace App\Services\Booking;

use App\Enums\CarOwnershipType;
use App\Models\Car;
use App\Models\Driver;
use App\Models\Trip;
use Carbon\CarbonImmutable;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Schema;

/**
 * The gatekeeper for trip booking creation (spec §6 Phase 5).
 *
 * checkAvailability() returns an AvailabilityResult listing every conflict found
 * so the caller (Filament TripResource form, future REST API, etc.) can show
 * all problems at once instead of one-at-a-time. Hard issues block booking;
 * soft issues can be overridden with a reason.
 *
 * Spec-listed conflict categories:
 *   1. carConflict — other non-cancelled trip on this car overlapping ±2hr buffer (HARD)
 *   2. driverConflict — same for driver (HARD)
 *   3. maintenanceConflict — scheduled/in-service maintenance_order on this car in window (HARD)
 *      [Phase 6 will populate this; for Phase 5 it's guarded by Schema::hasTable.]
 *   4. carDocumentExpiry — active car_document expiring inside the window (SOFT)
 *   5. driverDocumentExpiry — same for driver docs (SOFT)
 *   6. subRentalCoverage — sub_rented car must have an active contract covering the window (HARD)
 */
class BookingAvailabilityService
{
    private const OVERLAP_BUFFER_HOURS = 2;

    public function checkAvailability(
        string $carId,
        string $driverId,
        CarbonImmutable $scheduledStart,
        CarbonImmutable $scheduledEnd,
        ?string $excludeTripId = null,
    ): AvailabilityResult {
        $issues = [];

        $bufferedStart = $scheduledStart->subHours(self::OVERLAP_BUFFER_HOURS);
        $bufferedEnd = $scheduledEnd->addHours(self::OVERLAP_BUFFER_HOURS);

        // 1. Car overlap (other active trips on this car)
        foreach ($this->findOverlappingTrips('car_id', $carId, $bufferedStart, $bufferedEnd, $excludeTripId) as $trip) {
            $issues[] = new AvailabilityIssue(
                type: 'car_conflict',
                severity: 'hard',
                message: "Car already booked on trip {$trip->trip_number} ({$trip->scheduled_start->toDateTimeString()} → {$trip->scheduled_end->toDateTimeString()})",
                conflictingModelId: $trip->id,
                conflictingModelClass: Trip::class,
            );
        }

        // 2. Driver overlap
        foreach ($this->findOverlappingTrips('driver_id', $driverId, $bufferedStart, $bufferedEnd, $excludeTripId) as $trip) {
            $issues[] = new AvailabilityIssue(
                type: 'driver_conflict',
                severity: 'hard',
                message: "Driver already booked on trip {$trip->trip_number} ({$trip->scheduled_start->toDateTimeString()} → {$trip->scheduled_end->toDateTimeString()})",
                conflictingModelId: $trip->id,
                conflictingModelClass: Trip::class,
            );
        }

        // 3. Maintenance overlap — Phase 6 lights this up automatically.
        if (Schema::hasTable('maintenance_orders')) {
            $orders = \DB::table('maintenance_orders')
                ->where('car_id', $carId)
                ->whereIn('status', ['scheduled', 'in_service'])
                ->whereNull('deleted_at')
                ->where('scheduled_start', '<', $scheduledEnd)
                ->where('scheduled_end', '>', $scheduledStart)
                ->get();
            foreach ($orders as $order) {
                $issues[] = new AvailabilityIssue(
                    type: 'maintenance_conflict',
                    severity: 'hard',
                    message: "Car has a {$order->status} maintenance order ({$order->scheduled_start} → {$order->scheduled_end}) overlapping the booking",
                    conflictingModelId: $order->id,
                );
            }
        }

        // 4. Car document expiry inside the window (active docs only)
        $car = Car::query()
            ->withoutGlobalScopes()
            ->with(['documents' => fn ($q) => $q->where('is_active', true)->whereNotNull('expiry_date')])
            ->find($carId);

        if ($car !== null) {
            foreach ($car->documents as $doc) {
                if ($doc->expiry_date->gte($scheduledStart) && $doc->expiry_date->lte($scheduledEnd)) {
                    $issues[] = new AvailabilityIssue(
                        type: 'car_document_expiry',
                        severity: 'soft',
                        message: "Car document ({$doc->doc_type->value}) expires on {$doc->expiry_date->toDateString()} — inside the trip window",
                        conflictingModelId: $doc->id,
                        conflictingModelClass: get_class($doc),
                    );
                }
            }

            // 6. Sub-rental coverage
            if ($car->ownership_type === CarOwnershipType::SubRented) {
                $contract = $car->activeSubRentalContract();
                if ($contract === null
                    || ! $contract->coversDateRange($scheduledStart->toMutable(), $scheduledEnd->toMutable())) {
                    $issues[] = new AvailabilityIssue(
                        type: 'sub_rental_coverage',
                        severity: 'hard',
                        message: 'Car is sub-rented but no active sub-rental contract covers the entire trip window.',
                        conflictingModelId: $car->id,
                        conflictingModelClass: Car::class,
                    );
                }
            }
        }

        // 5. Driver document expiry inside the window
        $driver = Driver::query()
            ->withoutGlobalScopes()
            ->with(['documents' => fn ($q) => $q->where('is_active', true)->whereNotNull('expiry_date')])
            ->find($driverId);

        if ($driver !== null) {
            foreach ($driver->documents as $doc) {
                if ($doc->expiry_date->gte($scheduledStart) && $doc->expiry_date->lte($scheduledEnd)) {
                    $issues[] = new AvailabilityIssue(
                        type: 'driver_document_expiry',
                        severity: 'soft',
                        message: "Driver document ({$doc->doc_type->value}) expires on {$doc->expiry_date->toDateString()} — inside the trip window",
                        conflictingModelId: $doc->id,
                        conflictingModelClass: get_class($doc),
                    );
                }
            }
        }

        return new AvailabilityResult($issues);
    }

    /**
     * @return Collection<int,Trip>
     */
    private function findOverlappingTrips(
        string $foreignKey,
        string $foreignId,
        CarbonImmutable $bufferedStart,
        CarbonImmutable $bufferedEnd,
        ?string $excludeTripId,
    ): Collection {
        return Trip::query()
            ->withoutGlobalScopes()
            ->where($foreignKey, $foreignId)
            ->whereNotIn('status', ['cancelled', 'no_show', 'completed', 'closed'])
            ->when($excludeTripId, fn ($q) => $q->where('id', '!=', $excludeTripId))
            ->where('scheduled_start', '<', $bufferedEnd)
            ->where('scheduled_end', '>', $bufferedStart)
            ->get();
    }
}
