<?php

declare(strict_types=1);

namespace App\Observers;

use App\Enums\CarStatus;
use App\Enums\MaintenanceOrderStatus;
use App\Models\MaintenanceOrder;
use App\Services\Maintenance\MaintenanceScheduleService;

/**
 * Spec §6 Phase 6 — "auto status flip on cars during in_service".
 *
 * - creating: auto-generate M-YYYY-NNNN order number
 * - updated: when status moves to InService, flip car.status = InMaintenance;
 *            when status moves to Completed/Cancelled and car is currently
 *            InMaintenance, flip back to Available.
 * - updated: on Completed, ask MaintenanceScheduleService to recompute the
 *            next_due_km / next_due_date of any matching schedule.
 */
class MaintenanceOrderObserver
{
    public function __construct(private readonly MaintenanceScheduleService $schedules) {}

    public function creating(MaintenanceOrder $order): void
    {
        if (empty($order->order_number)) {
            $order->order_number = MaintenanceOrder::nextNumber();
        }
    }

    public function updated(MaintenanceOrder $order): void
    {
        if (! $order->wasChanged('status')) {
            return;
        }

        $car = $order->car;
        if ($car === null) {
            return;
        }

        match ($order->status) {
            MaintenanceOrderStatus::InService => $this->flipCarToMaintenance($order),
            MaintenanceOrderStatus::Completed, MaintenanceOrderStatus::Cancelled => $this->flipCarBackToAvailable($order),
            default => null,
        };

        if ($order->status === MaintenanceOrderStatus::Completed) {
            $this->schedules->recomputeNextDue($order);
        }
    }

    private function flipCarToMaintenance(MaintenanceOrder $order): void
    {
        if ($order->car && $order->car->status !== CarStatus::InMaintenance) {
            $order->car->status = CarStatus::InMaintenance;
            $order->car->save();
        }
    }

    private function flipCarBackToAvailable(MaintenanceOrder $order): void
    {
        if ($order->car && $order->car->status === CarStatus::InMaintenance) {
            $order->car->status = CarStatus::Available;
            $order->car->save();
        }
    }
}
