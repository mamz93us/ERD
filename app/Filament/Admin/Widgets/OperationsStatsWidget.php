<?php

declare(strict_types=1);

namespace App\Filament\Admin\Widgets;

use App\Enums\CarStatus;
use App\Enums\TripStatus;
use App\Models\Car;
use App\Models\Trip;
use Carbon\CarbonImmutable;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

/**
 * Spec §6 Phase 12 — operational stats (4 of the 8):
 *  - Active trips count
 *  - Fleet utilization % over the last 30 days
 *  - RevPACD = revenue per available car day
 *  - Cars in maintenance count
 *
 * Branch scoping is handled by BelongsToBranch global scope on the
 * underlying models (Trip/Car). super_admin sees all branches; others
 * see only their assigned branch's data.
 */
class OperationsStatsWidget extends StatsOverviewWidget
{
    protected ?string $heading = null;

    protected static ?int $sort = 1;

    /** @return array<Stat> */
    protected function getStats(): array
    {
        $now = CarbonImmutable::now();
        $monthAgo = $now->subDays(30);

        $activeTrips = Trip::query()
            ->whereIn('status', [
                TripStatus::Confirmed,
                TripStatus::Assigned,
                TripStatus::EnRoute,
                TripStatus::InProgress,
            ])
            ->count();

        $inMaintenance = Car::query()->where('status', CarStatus::InMaintenance)->count();

        $fleetCount = Car::query()->whereNotIn('status', [CarStatus::OutOfService])->count();
        $availableCarDays = max(1, $fleetCount * 30);

        $tripHoursLast30 = Trip::query()
            ->whereIn('status', [TripStatus::Completed, TripStatus::Invoiced, TripStatus::Closed])
            ->whereBetween('scheduled_end', [$monthAgo, $now])
            ->get(['scheduled_start', 'scheduled_end'])
            ->sum(function (Trip $t): float {
                if ($t->scheduled_start === null || $t->scheduled_end === null) {
                    return 0;
                }

                return max(0, CarbonImmutable::parse($t->scheduled_start)->diffInHours(CarbonImmutable::parse($t->scheduled_end)));
            });

        $availableHours = $availableCarDays * 24;
        $utilization = $availableHours > 0 ? round(($tripHoursLast30 / $availableHours) * 100, 1) : 0.0;

        $revenueLast30 = (string) \DB::table('invoices')
            ->whereNotIn('status', ['cancelled', 'draft'])
            ->whereBetween('issue_date', [$monthAgo->toDateString(), $now->toDateString()])
            ->whereNull('deleted_at')
            ->sum('total');

        $revPacd = $availableCarDays > 0
            ? bcdiv($revenueLast30, (string) $availableCarDays, 2)
            : '0.00';

        return [
            Stat::make(__('widgets.active_trips'), (string) $activeTrips)
                ->description(__('widgets.active_trips_desc'))
                ->color('primary'),
            Stat::make(__('widgets.fleet_utilization'), $utilization.'%')
                ->description(__('widgets.fleet_utilization_desc'))
                ->color($utilization >= 60 ? 'success' : ($utilization >= 35 ? 'warning' : 'danger')),
            Stat::make(__('widgets.revpacd'), 'EGP '.number_format((float) $revPacd, 2))
                ->description(__('widgets.revpacd_desc'))
                ->color('info'),
            Stat::make(__('widgets.cars_in_maintenance'), (string) $inMaintenance)
                ->description(__('widgets.cars_in_maintenance_desc'))
                ->color($inMaintenance > 0 ? 'warning' : 'success'),
        ];
    }
}
