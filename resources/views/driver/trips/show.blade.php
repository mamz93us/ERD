@extends('driver.layout')

@php
    $canStart = in_array($trip->status?->value, ['confirmed', 'assigned', 'en_route'], true);
    $canEnd = $trip->status?->value === 'in_progress';
@endphp

@section('content')
    <a href="{{ route('driver.dashboard') }}" class="text-sm text-amber-600 mb-3 inline-block">← {{ __('driver.back') }}</a>

    <div class="bg-white rounded-2xl shadow-sm p-5 mb-4 border border-slate-200">
        <div class="flex items-center justify-between mb-2">
            <h1 class="text-xl font-bold">{{ $trip->trip_number }}</h1>
            <span class="text-xs text-white px-2 py-1 rounded-full bg-sky-500">{{ $trip->status?->getLabel() }}</span>
        </div>

        <div class="grid grid-cols-2 gap-3 mt-4 text-sm">
            <div>
                <div class="text-xs text-slate-500">{{ __('driver.customer') }}</div>
                <div class="font-semibold">{{ $trip->customer?->full_name }}</div>
                @if($trip->customer?->phone)
                    <a href="tel:{{ $trip->customer->phone }}" class="text-xs text-amber-600">📞 {{ $trip->customer->phone }}</a>
                @endif
            </div>
            <div>
                <div class="text-xs text-slate-500">{{ __('driver.car') }}</div>
                <div class="font-semibold">{{ $trip->car?->plate }}</div>
                <div class="text-xs text-slate-500">{{ $trip->car?->make }} {{ $trip->car?->model }}</div>
            </div>
            <div>
                <div class="text-xs text-slate-500">{{ __('driver.pickup') }}</div>
                <div class="font-semibold">📍 {{ $trip->pickup_location }}</div>
                <div class="text-xs text-slate-500 mt-0.5">{{ \Illuminate\Support\Carbon::parse($trip->scheduled_start)->format('Y-m-d H:i') }}</div>
            </div>
            <div>
                <div class="text-xs text-slate-500">{{ __('driver.dropoff') }}</div>
                <div class="font-semibold">📍 {{ $trip->dropoff_location }}</div>
                <div class="text-xs text-slate-500 mt-0.5">{{ \Illuminate\Support\Carbon::parse($trip->scheduled_end)->format('Y-m-d H:i') }}</div>
            </div>
        </div>

        @if($trip->actual_start)
            <div class="mt-4 pt-3 border-t border-slate-200 text-xs text-slate-600">
                <div>{{ __('driver.started_at') }}: {{ \Illuminate\Support\Carbon::parse($trip->actual_start)->format('Y-m-d H:i') }} · {{ __('driver.start_km') }}: {{ number_format((int) ($trip->start_odometer ?? 0)) }}</div>
                @if($trip->actual_end)
                    <div>{{ __('driver.ended_at') }}: {{ \Illuminate\Support\Carbon::parse($trip->actual_end)->format('Y-m-d H:i') }} · {{ __('driver.end_km') }}: {{ number_format((int) ($trip->end_odometer ?? 0)) }}</div>
                @endif
            </div>
        @endif
    </div>

    @if($canStart)
        <form method="POST" action="{{ route('driver.trips.start', $trip->id) }}" class="bg-white rounded-2xl shadow-sm p-5 mb-4 border border-slate-200">
            @csrf
            <h2 class="font-bold mb-3">{{ __('driver.start_trip') }}</h2>
            <label class="block text-sm text-slate-700 mb-1">{{ __('driver.start_odometer') }}</label>
            <input type="number" name="start_odometer" required min="0" value="{{ $trip->car?->current_odometer }}"
                   class="w-full rounded-lg border border-slate-300 px-3 py-3 text-base mb-3" />
            <button type="submit" class="w-full bg-emerald-500 hover:bg-emerald-600 text-white font-bold rounded-lg py-3 shadow-md">
                ▶ {{ __('driver.start_button') }}
            </button>
        </form>
    @endif

    @if($canEnd)
        <form method="POST" action="{{ route('driver.trips.end', $trip->id) }}" class="bg-white rounded-2xl shadow-sm p-5 mb-4 border border-slate-200">
            @csrf
            <h2 class="font-bold mb-3">{{ __('driver.end_trip') }}</h2>
            <label class="block text-sm text-slate-700 mb-1">{{ __('driver.end_odometer') }}</label>
            <input type="number" name="end_odometer" required min="{{ (int) ($trip->start_odometer ?? 0) }}"
                   class="w-full rounded-lg border border-slate-300 px-3 py-3 text-base mb-3" />
            <button type="submit" class="w-full bg-rose-500 hover:bg-rose-600 text-white font-bold rounded-lg py-3 shadow-md">
                ⏹ {{ __('driver.end_button') }}
            </button>
        </form>
    @endif

    <a href="{{ route('driver.expenses.create', $trip->id) }}"
       class="block bg-white border-2 border-dashed border-slate-300 rounded-2xl p-4 text-center text-slate-600 hover:border-amber-400 hover:text-amber-700 transition">
        + {{ __('driver.add_expense') }}
    </a>
@endsection
