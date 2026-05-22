<?php

declare(strict_types=1);

namespace App\Http\Controllers\Driver;

use App\Enums\TripStatus;
use App\Http\Controllers\Controller;
use App\Models\Driver;
use App\Models\Trip;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class TripController extends Controller
{
    public function show(string $tripId): View
    {
        $trip = $this->driverTrip($tripId);
        $trip->load(['customer:id,full_name,full_name_ar,phone', 'car:id,plate,make,model,current_odometer']);

        return view('driver.trips.show', ['trip' => $trip]);
    }

    public function start(Request $request, string $tripId): RedirectResponse
    {
        $trip = $this->driverTrip($tripId);

        if (! in_array($trip->status, [TripStatus::Confirmed, TripStatus::Assigned, TripStatus::EnRoute], true)) {
            return back()->withErrors(['status' => __('driver.cannot_start_in_status', ['status' => $trip->status?->getLabel()])]);
        }

        $data = $request->validate([
            'start_odometer' => ['required', 'integer', 'min:0'],
        ]);

        $trip->forceFill([
            'actual_start' => now(),
            'start_odometer' => $data['start_odometer'],
            'status' => TripStatus::InProgress,
        ])->save();

        return redirect()
            ->route('driver.trips.show', $trip->id)
            ->with('status', __('driver.trip_started'));
    }

    public function end(Request $request, string $tripId): RedirectResponse
    {
        $trip = $this->driverTrip($tripId);

        if ($trip->status !== TripStatus::InProgress) {
            return back()->withErrors(['status' => __('driver.not_in_progress')]);
        }

        $data = $request->validate([
            'end_odometer' => ['required', 'integer', 'min:'.((int) ($trip->start_odometer ?? 0))],
        ]);

        $trip->forceFill([
            'actual_end' => now(),
            'end_odometer' => $data['end_odometer'],
            'status' => TripStatus::Completed,
        ])->save();

        return redirect()
            ->route('driver.dashboard')
            ->with('status', __('driver.trip_completed'));
    }

    private function driverTrip(string $tripId): Trip
    {
        /** @var Driver $driver */
        $driver = Auth::guard('driver')->user();

        return Trip::query()
            ->withoutGlobalScopes()
            ->where('driver_id', $driver->id)
            ->findOrFail($tripId);
    }
}
