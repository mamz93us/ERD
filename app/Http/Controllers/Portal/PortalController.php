<?php

declare(strict_types=1);

namespace App\Http\Controllers\Portal;

use App\Enums\InvoiceStatus;
use App\Enums\LeadSource;
use App\Enums\LeadStatus;
use App\Enums\QuotationStatus;
use App\Enums\TripStatus;
use App\Http\Controllers\Controller;
use App\Models\Customer;
use App\Models\Invoice;
use App\Models\Lead;
use App\Models\Quotation;
use App\Models\Trip;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\CarbonImmutable;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

/**
 * Phase 11 portal pages. One controller because the actions are small and
 * share the auth guard + branch-less query scoping. Spec §6 Phase 11 calls
 * for 7 pages: dashboard, request booking, view quotations, active trips,
 * trip history, invoices, profile, documents.
 */
class PortalController extends Controller
{
    public function dashboard(): View
    {
        $customer = $this->me();

        $today = CarbonImmutable::today();

        $activeTrips = Trip::query()
            ->withoutGlobalScopes()
            ->where('customer_id', $customer->id)
            ->whereNotIn('status', [TripStatus::Cancelled, TripStatus::NoShow, TripStatus::Closed])
            ->where('scheduled_end', '>=', $today)
            ->orderBy('scheduled_start')
            ->limit(5)
            ->get();

        $unpaidInvoices = Invoice::query()
            ->withoutGlobalScopes()
            ->where('customer_id', $customer->id)
            ->whereNotIn('status', [InvoiceStatus::Paid, InvoiceStatus::Cancelled])
            ->orderBy('due_date')
            ->limit(5)
            ->get();

        $openQuotations = Quotation::query()
            ->withoutGlobalScopes()
            ->where('customer_id', $customer->id)
            ->whereIn('status', [QuotationStatus::Draft, QuotationStatus::Sent])
            ->orderByDesc('created_at')
            ->limit(5)
            ->get();

        $balanceDue = (string) Invoice::query()
            ->withoutGlobalScopes()
            ->where('customer_id', $customer->id)
            ->whereNotIn('status', [InvoiceStatus::Paid, InvoiceStatus::Cancelled])
            ->sum('balance_due');

        return view('portal.dashboard', compact('customer', 'activeTrips', 'unpaidInvoices', 'openQuotations', 'balanceDue'));
    }

    public function trips(): View
    {
        $customer = $this->me();

        $trips = Trip::query()
            ->withoutGlobalScopes()
            ->where('customer_id', $customer->id)
            ->with(['car:id,plate,make,model', 'driver:id,full_name'])
            ->orderByDesc('scheduled_start')
            ->paginate(20);

        return view('portal.trips.index', compact('trips'));
    }

    public function tripShow(string $tripId): View
    {
        $customer = $this->me();

        $trip = Trip::query()
            ->withoutGlobalScopes()
            ->with(['car:id,plate,make,model', 'driver:id,full_name,phone', 'invoices'])
            ->where('customer_id', $customer->id)
            ->findOrFail($tripId);

        return view('portal.trips.show', compact('trip'));
    }

    public function quotations(): View
    {
        $customer = $this->me();

        $quotations = Quotation::query()
            ->withoutGlobalScopes()
            ->where('customer_id', $customer->id)
            ->orderByDesc('created_at')
            ->paginate(20);

        return view('portal.quotations.index', compact('quotations'));
    }

    public function quotationShow(string $id): View
    {
        $customer = $this->me();

        $quotation = Quotation::query()
            ->withoutGlobalScopes()
            ->with(['category', 'rateCard'])
            ->where('customer_id', $customer->id)
            ->findOrFail($id);

        return view('portal.quotations.show', compact('quotation'));
    }

    public function quotationDecide(Request $request, string $id): RedirectResponse
    {
        $customer = $this->me();

        $quotation = Quotation::query()
            ->withoutGlobalScopes()
            ->where('customer_id', $customer->id)
            ->findOrFail($id);

        $action = $request->validate(['action' => ['required', 'in:accept,reject']])['action'];

        if (! in_array($quotation->status, [QuotationStatus::Draft, QuotationStatus::Sent], true)) {
            return back()->withErrors(['action' => __('portal.quotation_not_decidable')]);
        }

        $quotation->update(['status' => $action === 'accept' ? QuotationStatus::Accepted : QuotationStatus::Rejected]);

        return redirect()
            ->route('portal.quotations.show', $quotation->id)
            ->with('status', __($action === 'accept' ? 'portal.quotation_accepted' : 'portal.quotation_rejected'));
    }

    public function invoices(): View
    {
        $customer = $this->me();

        $invoices = Invoice::query()
            ->withoutGlobalScopes()
            ->where('customer_id', $customer->id)
            ->orderByDesc('issue_date')
            ->paginate(20);

        return view('portal.invoices.index', compact('invoices'));
    }

    public function invoicePdf(string $id)
    {
        $customer = $this->me();

        $invoice = Invoice::query()
            ->withoutGlobalScopes()
            ->with(['customer', 'corporateAccount', 'lines'])
            ->where('customer_id', $customer->id)
            ->findOrFail($id);

        $locale = $customer->preferredLocale();
        $pdf = Pdf::loadView('pdfs.invoice', compact('invoice', 'locale'));

        return response()->streamDownload(
            fn () => print ($pdf->output()),
            "{$invoice->invoice_number}.pdf",
            ['Content-Type' => 'application/pdf'],
        );
    }

    public function bookingRequestForm(): View
    {
        return view('portal.booking.create');
    }

    public function bookingRequestStore(Request $request): RedirectResponse
    {
        $customer = $this->me();

        $data = $request->validate([
            'pickup_at' => ['required', 'date', 'after:now'],
            'dropoff_at' => ['required', 'date', 'after:pickup_at'],
            'pickup_location' => ['required', 'string', 'max:255'],
            'dropoff_location' => ['required', 'string', 'max:255'],
            'estimated_distance_km' => ['nullable', 'integer', 'min:0'],
            'requirements' => ['required', 'string', 'min:5', 'max:2000'],
        ]);

        Lead::create([
            'customer_id' => $customer->id,
            'assigned_user_id' => null,
            'source' => LeadSource::Website,
            'status' => LeadStatus::New_,
            'requirements' => sprintf(
                "From: %s\nTo: %s\nPickup: %s\nDropoff: %s\nDistance: %s km\n\n%s",
                $data['pickup_location'],
                $data['dropoff_location'],
                $data['pickup_at'],
                $data['dropoff_at'],
                $data['estimated_distance_km'] ?? '—',
                $data['requirements'],
            ),
            'estimated_value' => 0,
        ]);

        return redirect()
            ->route('portal.dashboard')
            ->with('status', __('portal.booking_request_received'));
    }

    public function profile(): View
    {
        return view('portal.profile', ['customer' => $this->me()]);
    }

    private function me(): Customer
    {
        /** @var Customer $c */
        $c = Auth::guard('customer')->user();

        return $c;
    }
}
