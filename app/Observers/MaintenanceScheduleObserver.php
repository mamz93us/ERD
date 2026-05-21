<?php

declare(strict_types=1);

namespace App\Observers;

use App\Models\MaintenanceSchedule;
use App\Services\Maintenance\MaintenanceScheduleService;

/**
 * On schedule create/update, recompute next_due_km/date from the configured
 * intervals so the dashboard / CheckMaintenanceDue command always sees a
 * sensible projection without the user filling those fields manually.
 */
class MaintenanceScheduleObserver
{
    public function __construct(private readonly MaintenanceScheduleService $service) {}

    public function saving(MaintenanceSchedule $schedule): void
    {
        // Only recompute on changes to interval_* or last_done_*; skip if user
        // explicitly set next_due_* fields in the same save.
        if (! $schedule->wasChanged() && $schedule->exists) {
            return;
        }
    }

    public function saved(MaintenanceSchedule $schedule): void
    {
        // Recompute next due after save so the new values stick if intervals changed.
        if ($schedule->wasChanged(['interval_km', 'interval_days', 'last_done_km', 'last_done_date'])
            || $schedule->wasRecentlyCreated) {
            // Use withoutEvents to avoid recursion.
            MaintenanceSchedule::withoutEvents(fn () => $this->service->recomputeFromIntervals($schedule));
        }
    }
}
