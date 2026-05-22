@extends('portal.layout')

@php $isAr = app()->getLocale() === 'ar'; @endphp

@section('content')
    <div class="bg-gradient-to-br from-slate-900 to-slate-700 text-white rounded-2xl p-5 shadow-md mb-5">
        <div class="text-xs opacity-90">{{ __('portal.welcome_back') }}</div>
        <div class="text-2xl font-bold mt-1">{{ $isAr ? ($customer->full_name_ar ?? $customer->full_name) : $customer->full_name }}</div>
        @if(bccomp($balanceDue, '0.00', 2) > 0)
            <div class="mt-3 inline-block bg-rose-500 text-white rounded-full px-3 py-1 text-sm font-semibold">
                {{ __('portal.balance_due') }}: EGP {{ number_format((float) $balanceDue, 2) }}
            </div>
        @else
            <div class="mt-3 inline-block bg-emerald-500 text-white rounded-full px-3 py-1 text-sm font-semibold">
                {{ __('portal.no_balance') }}
            </div>
        @endif
    </div>

    <a href="{{ route('portal.booking.create') }}"
       class="block mb-5 bg-amber-500 hover:bg-amber-600 text-white rounded-2xl p-5 text-center font-bold shadow-md transition">
        + {{ __('portal.request_booking') }}
    </a>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
        <section>
            <h2 class="font-bold mb-2">{{ __('portal.active_trips') }} ({{ $activeTrips->count() }})</h2>
            @forelse($activeTrips as $trip)
                <a href="{{ route('portal.trips.show', $trip->id) }}" class="block mb-2 bg-white rounded-xl p-3 shadow-sm border border-slate-200 hover:border-amber-400 transition">
                    <div class="flex items-center justify-between">
                        <span class="font-semibold text-sm">{{ $trip->trip_number }}</span>
                        <span class="text-xs text-slate-500">{{ \Illuminate\Support\Carbon::parse($trip->scheduled_start)->format('M j, H:i') }}</span>
                    </div>
                    <div class="text-xs text-slate-600 mt-1">📍 {{ $trip->pickup_location }} → {{ $trip->dropoff_location }}</div>
                </a>
            @empty
                <div class="bg-white rounded-xl p-4 text-center text-slate-500 text-sm border border-slate-200">{{ __('portal.no_active_trips') }}</div>
            @endforelse
        </section>

        <section>
            <h2 class="font-bold mb-2">{{ __('portal.open_quotations') }} ({{ $openQuotations->count() }})</h2>
            @forelse($openQuotations as $q)
                <a href="{{ route('portal.quotations.show', $q->id) }}" class="block mb-2 bg-white rounded-xl p-3 shadow-sm border border-slate-200">
                    <div class="flex items-center justify-between">
                        <span class="font-semibold text-sm">{{ $q->quotation_number }}</span>
                        <span class="text-xs font-bold text-emerald-600">EGP {{ number_format((float) $q->total_amount, 0) }}</span>
                    </div>
                    <div class="text-xs text-slate-500 mt-1">{{ __('portal.valid_until') }}: {{ \Illuminate\Support\Carbon::parse($q->valid_until)->format('Y-m-d') }}</div>
                </a>
            @empty
                <div class="bg-white rounded-xl p-4 text-center text-slate-500 text-sm border border-slate-200">{{ __('portal.no_open_quotations') }}</div>
            @endforelse
        </section>

        <section class="md:col-span-2">
            <h2 class="font-bold mb-2">{{ __('portal.unpaid_invoices') }} ({{ $unpaidInvoices->count() }})</h2>
            @forelse($unpaidInvoices as $inv)
                <a href="{{ route('portal.invoices.pdf', $inv->id) }}" class="block mb-2 bg-white rounded-xl p-3 shadow-sm border border-slate-200">
                    <div class="flex items-center justify-between">
                        <span class="font-semibold text-sm">{{ $inv->invoice_number }}</span>
                        <span class="text-xs font-bold text-rose-600">{{ __('portal.due_in') }} {{ number_format((float) $inv->balance_due, 2) }} EGP</span>
                    </div>
                    <div class="text-xs text-slate-500 mt-1">{{ __('portal.due') }}: {{ \Illuminate\Support\Carbon::parse($inv->due_date)->format('Y-m-d') }}</div>
                </a>
            @empty
                <div class="bg-white rounded-xl p-4 text-center text-slate-500 text-sm border border-slate-200">{{ __('portal.no_unpaid_invoices') }}</div>
            @endforelse
        </section>
    </div>
@endsection
