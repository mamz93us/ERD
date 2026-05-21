<?php

declare(strict_types=1);

namespace App\Services\Maintenance;

use App\Enums\MaintenanceOrderStatus;
use App\Models\MaintenanceOrder;
use App\Models\MaintenanceSchedule;
use Carbon\Carbon;

/**
 * Spec §6 Phase 6: recompute a maintenance_schedule's next_due_km / next_due_date
 * after a maintenance_order completes, using the schedule's interval_km / interval_days.
 *
 * Also called by CheckMaintenanceDue to project the "due now" window.
 */
class MaintenanceScheduleService
{
    /**
     * Update the schedule's last_done_* and next_due_* from a completed order.
     * No-op if there's no schedule for this car+service_type.
     */
    public function recomputeNextDue(MaintenanceOrder $completedOrder): ?MaintenanceSchedule
    {
        if ($completedOrder->status !== MaintenanceOrderStatus::Completed) {
            return null;
        }

        // Match the schedule for this car. service_type is not stored on the order itself
        // (one order can cover multiple service types via items), so we find any active
        // schedule and rebase from the actual completion date/odometer for each.
        $schedules = MaintenanceSchedule::query()
            ->where('car_id', $completedOrder->car_id)
            ->where('is_active', true)
            ->get();

        $first = null;
        foreach ($schedules as $schedule) {
            $this->rebase($schedule, $completedOrder);
            $first ??= $schedule;
        }

        return $first;
    }

    /**
     * Recompute next_due_km + next_due_date from the schedule's intervals.
     * Used on creation and after a completed order rebases last_done_*.
     */
    public function recomputeFromIntervals(MaintenanceSchedule $schedule): void
    {
        $baseKm = $schedule->last_done_km ?? (int) ($schedule->car?->current_odometer ?? 0);
        $baseDate = $schedule->last_done_date ?? Carbon::now();

        if ($schedule->interval_km !== null) {
            $schedule->next_due_km = $baseKm + (int) $schedule->interval_km;
        }
        if ($schedule->interval_days !== null) {
            $schedule->next_due_date = $baseDate->copy()->addDays((int) $schedule->interval_days);
        }
        $schedule->save();
    }

    private function rebase(MaintenanceSchedule $schedule, MaintenanceOrder $order): void
    {
        $completedAt = $order->actual_end ?? $order->scheduled_end ?? Carbon::now();
        $odometer = (int) ($order->odometer_at_service ?? $schedule->car?->current_odometer ?? 0);

        $schedule->last_done_date = $completedAt->copy()->startOfDay();
        $schedule->last_done_km = $odometer;

        $this->recomputeFromIntervals($schedule);
    }
}
