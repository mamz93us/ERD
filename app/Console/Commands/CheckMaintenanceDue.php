<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Enums\MaintenanceOrderStatus;
use App\Enums\MaintenanceOrderType;
use App\Models\Garage;
use App\Models\MaintenanceOrder;
use App\Models\MaintenanceSchedule;
use Illuminate\Console\Command;

/**
 * Daily sweep per spec §6 Phase 6.
 *
 * For each active schedule, if it's "due now" (next_due_date <= today OR
 * next_due_km <= the car's current_odometer), create a draft (status=scheduled)
 * maintenance_order — unless a pending one already exists for that car +
 * service window. Default garage is the internal one (or first active garage).
 *
 * Scheduled daily at 06:30 Africa/Cairo via routes/console.php.
 */
class CheckMaintenanceDue extends Command
{
    protected $signature = 'maintenance:check-due';

    protected $description = 'Create draft maintenance orders for schedules that are due (date or km)';

    public function handle(): int
    {
        $today = now()->startOfDay()->toDateString();
        $defaultGarage = Garage::query()
            ->where('is_active', true)
            ->orderByDesc('is_internal')
            ->first();

        if ($defaultGarage === null) {
            $this->warn('No active garage configured — skipping due-sweep.');

            return self::INVALID;
        }

        $created = 0;

        MaintenanceSchedule::query()
            ->with('car')
            ->where('is_active', true)
            ->cursor()
            ->each(function (MaintenanceSchedule $schedule) use ($today, $defaultGarage, &$created): void {
                $car = $schedule->car;
                if ($car === null) {
                    return;
                }

                $dueByDate = $schedule->next_due_date !== null
                    && $schedule->next_due_date->toDateString() <= $today;
                $dueByKm = $schedule->next_due_km !== null
                    && (int) $car->current_odometer >= (int) $schedule->next_due_km;

                if (! $dueByDate && ! $dueByKm) {
                    return;
                }

                $alreadyPending = MaintenanceOrder::query()
                    ->where('car_id', $car->id)
                    ->whereIn('status', [
                        MaintenanceOrderStatus::Scheduled->value,
                        MaintenanceOrderStatus::InService->value,
                    ])
                    ->exists();

                if ($alreadyPending) {
                    return;
                }

                MaintenanceOrder::query()->create([
                    'car_id' => $car->id,
                    'garage_id' => $defaultGarage->id,
                    'order_type' => MaintenanceOrderType::Preventive,
                    'description' => "Auto-created from schedule: {$schedule->service_type->value}",
                    'scheduled_start' => now()->addDays(1)->setTime(9, 0),
                    'scheduled_end' => now()->addDays(1)->setTime(17, 0),
                    'status' => MaintenanceOrderStatus::Scheduled,
                ]);

                $created++;
            });

        $this->info("Maintenance due-sweep: {$created} draft order(s) created");

        return self::SUCCESS;
    }
}
