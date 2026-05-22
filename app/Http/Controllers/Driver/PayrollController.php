<?php

declare(strict_types=1);

namespace App\Http\Controllers\Driver;

use App\Enums\TripStatus;
use App\Http\Controllers\Controller;
use App\Models\Driver;
use App\Models\TrafficFine;
use App\Models\Trip;
use Carbon\CarbonImmutable;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

/**
 * Read-only payroll view per spec §9.2:
 *
 *   commission_amount = trip.subtotal × driver.trip_commission_percentage / 100
 *
 * Net payable for a period = sum(commissions on completed trips)
 *                          − sum(traffic_fines.amount where deducted_from_driver = true)
 *
 * v1 computes on the fly. Phase 13 hardening adds the `driver_earnings`
 * snapshot rows + a monthly settlement workflow.
 */
class PayrollController extends Controller
{
    public function index(): View
    {
        /** @var Driver $driver */
        $driver = Auth::guard('driver')->user();

        $periodStart = CarbonImmutable::today()->startOfMonth();
        $periodEnd = CarbonImmutable::today()->endOfMonth();

        $trips = Trip::query()
            ->withoutGlobalScopes()
            ->where('driver_id', $driver->id)
            ->whereIn('status', [TripStatus::Completed, TripStatus::Invoiced, TripStatus::Closed])
            ->whereBetween('scheduled_end', [$periodStart, $periodEnd])
            ->get(['id', 'trip_number', 'subtotal', 'scheduled_end']);

        $pct = (string) ($driver->trip_commission_percentage ?? '0');
        $commission = '0.00';
        $tripRows = [];

        foreach ($trips as $trip) {
            $amount = bcdiv(bcmul((string) $trip->subtotal, $pct, 4), '100', 2);
            $commission = bcadd($commission, $amount, 2);

            $tripRows[] = [
                'trip_number' => $trip->trip_number,
                'date' => $trip->scheduled_end?->format('Y-m-d'),
                'subtotal' => (string) $trip->subtotal,
                'commission' => $amount,
            ];
        }

        $fines = TrafficFine::query()
            ->where('driver_id', $driver->id)
            ->where('deducted_from_driver', true)
            ->whereBetween('violation_date', [$periodStart->startOfDay(), $periodEnd->endOfDay()])
            ->get(['violation_number', 'violation_date', 'amount']);

        $finesTotal = '0.00';
        foreach ($fines as $f) {
            $finesTotal = bcadd($finesTotal, (string) $f->amount, 2);
        }

        $net = bcsub($commission, $finesTotal, 2);

        return view('driver.payroll', [
            'driver' => $driver,
            'periodStart' => $periodStart,
            'periodEnd' => $periodEnd,
            'commissionPct' => $pct,
            'commissionTotal' => $commission,
            'finesTotal' => $finesTotal,
            'netPayable' => $net,
            'tripRows' => $tripRows,
            'fines' => $fines,
        ]);
    }
}
