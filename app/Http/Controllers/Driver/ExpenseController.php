<?php

declare(strict_types=1);

namespace App\Http\Controllers\Driver;

use App\Enums\TripExpenseType;
use App\Http\Controllers\Controller;
use App\Models\Driver;
use App\Models\Trip;
use App\Models\TripExpense;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class ExpenseController extends Controller
{
    public function create(string $tripId): View
    {
        $trip = $this->driverTrip($tripId);

        return view('driver.expenses.create', ['trip' => $trip]);
    }

    public function store(Request $request, string $tripId): RedirectResponse
    {
        $trip = $this->driverTrip($tripId);

        $data = $request->validate([
            'type' => ['required', 'string', 'in:'.implode(',', array_column(TripExpenseType::cases(), 'value'))],
            'amount' => ['required', 'numeric', 'min:0.01'],
            'notes' => ['nullable', 'string', 'max:500'],
            'receipt' => ['nullable', 'image', 'max:5120'],
        ]);

        $receiptPath = null;
        if ($request->hasFile('receipt')) {
            $receiptPath = $request->file('receipt')->store('trip-expenses', 'public');
        }

        TripExpense::create([
            'trip_id' => $trip->id,
            'type' => $data['type'],
            'amount' => $data['amount'],
            'receipt_path' => $receiptPath,
            'reimbursed' => false,
            'notes' => $data['notes'] ?? null,
            'incurred_at' => now(),
        ]);

        return redirect()
            ->route('driver.trips.show', $trip->id)
            ->with('status', __('driver.expense_submitted'));
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
