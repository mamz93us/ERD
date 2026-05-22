<?php

declare(strict_types=1);

namespace App\Http\Controllers\Driver;

use App\Http\Controllers\Controller;
use App\Models\Driver;
use App\Models\Trip;
use Carbon\CarbonImmutable;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(): View
    {
        /** @var Driver $driver */
        $driver = Auth::guard('driver')->user();

        $today = CarbonImmutable::today();
        $todayTrips = Trip::query()
            ->withoutGlobalScopes()
            ->with(['customer:id,full_name,full_name_ar', 'car:id,plate,make,model'])
            ->where('driver_id', $driver->id)
            ->whereDate('scheduled_start', '<=', $today)
            ->whereDate('scheduled_end', '>=', $today)
            ->orderBy('scheduled_start')
            ->get();

        $upcoming = Trip::query()
            ->withoutGlobalScopes()
            ->with(['customer:id,full_name,full_name_ar', 'car:id,plate,make,model'])
            ->where('driver_id', $driver->id)
            ->where('scheduled_start', '>', $today->endOfDay())
            ->whereNotIn('status', ['cancelled', 'no_show', 'completed', 'closed'])
            ->orderBy('scheduled_start')
            ->limit(5)
            ->get();

        return view('driver.dashboard', [
            'driver' => $driver,
            'todayTrips' => $todayTrips,
            'upcoming' => $upcoming,
        ]);
    }
}
