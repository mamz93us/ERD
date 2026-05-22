@extends('portal.layout')

@section('content')
<a href="{{ route('portal.trips.index') }}" class="text-sm text-amber-600 mb-3 inline-block">← {{ __('portal.back_to_trips') }}</a>

<div class="bg-white rounded-2xl shadow-sm p-5 border border-slate-200">
    <div class="flex items-center justify-between mb-3">
        <h1 class="text-xl font-bold">{{ $trip->trip_number }}</h1>
        <span class="text-xs text-white px-2 py-1 rounded-full bg-sky-500">{{ $trip->status?->getLabel() }}</span>
    </div>

    <div class="grid grid-cols-2 gap-3 text-sm">
        <div>
            <div class="text-xs text-slate-500">{{ __('portal.driver') }}</div>
            <div class="font-semibold">{{ $trip->driver?->full_name ?? '—' }}</div>
            @if($trip->driver?->phone)<a href="tel:{{ $trip->driver->phone }}" class="text-xs text-amber-600">📞 {{ $trip->driver->phone }}</a>@endif
        </div>
        <div>
            <div class="text-xs text-slate-500">{{ __('portal.car') }}</div>
            <div class="font-semibold">{{ $trip->car?->plate }}</div>
            <div class="text-xs text-slate-500">{{ $trip->car?->make }} {{ $trip->car?->model }}</div>
        </div>
        <div>
            <div class="text-xs text-slate-500">{{ __('portal.pickup') }}</div>
            <div class="font-semibold">📍 {{ $trip->pickup_location }}</div>
            <div class="text-xs text-slate-500">{{ \Illuminate\Support\Carbon::parse($trip->scheduled_start)->format('Y-m-d H:i') }}</div>
        </div>
        <div>
            <div class="text-xs text-slate-500">{{ __('portal.dropoff') }}</div>
            <div class="font-semibold">📍 {{ $trip->dropoff_location }}</div>
            <div class="text-xs text-slate-500">{{ \Illuminate\Support\Carbon::parse($trip->scheduled_end)->format('Y-m-d H:i') }}</div>
        </div>
    </div>

    @if($trip->total_amount)
        <div class="mt-4 pt-3 border-t border-slate-200 flex items-center justify-between">
            <span class="text-xs text-slate-500">{{ __('portal.total') }}</span>
            <span class="font-bold text-lg">EGP {{ number_format((float) $trip->total_amount, 2) }}</span>
        </div>
    @endif
</div>

@if($trip->invoices->isNotEmpty())
    <div class="mt-4">
        <h2 class="font-bold mb-2">{{ __('portal.related_invoices') }}</h2>
        @foreach($trip->invoices as $inv)
            <a href="{{ route('portal.invoices.pdf', $inv->id) }}"
               class="block mb-2 bg-white rounded-xl p-3 shadow-sm border border-slate-200">
                <div class="flex items-center justify-between">
                    <span class="font-semibold">{{ $inv->invoice_number }}</span>
                    <span class="text-xs">{{ number_format((float) $inv->total, 2) }} EGP</span>
                </div>
            </a>
        @endforeach
    </div>
@endif
@endsection
