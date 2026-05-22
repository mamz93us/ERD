@extends('driver.layout')

@php $isAr = app()->getLocale() === 'ar'; @endphp

@section('content')
    <div class="bg-gradient-to-br from-amber-500 to-amber-600 text-white rounded-2xl p-5 shadow-md mb-5">
        <div class="text-xs opacity-90">{{ __('driver.welcome') }}</div>
        <div class="text-2xl font-bold mt-1">{{ $isAr ? ($driver->full_name_ar ?? $driver->full_name) : $driver->full_name }}</div>
        <div class="flex items-center gap-3 mt-3 text-sm">
            <span class="bg-white/20 rounded-full px-2 py-0.5">{{ __('driver.rating') }}: {{ number_format((float) ($driver->rating ?? 0), 1) }} ★</span>
            <span class="bg-white/20 rounded-full px-2 py-0.5">{{ __('driver.commission') }}: {{ number_format((float) ($driver->trip_commission_percentage ?? 0), 1) }}%</span>
        </div>
    </div>

    <section class="mb-6">
        <h2 class="text-lg font-bold text-slate-900 mb-3">{{ __('driver.today_trips') }} <span class="text-sm text-slate-400">({{ $todayTrips->count() }})</span></h2>
        @forelse($todayTrips as $trip)
            @php
                $bg = match($trip->status?->value) {
                    'in_progress', 'en_route' => 'bg-orange-500',
                    'completed', 'invoiced', 'closed' => 'bg-emerald-500',
                    default => 'bg-sky-500',
                };
            @endphp
            <a href="{{ route('driver.trips.show', $trip->id) }}" class="block mb-3 bg-white rounded-xl p-4 shadow-sm border border-slate-200 hover:border-amber-400 transition">
                <div class="flex items-center justify-between mb-2">
                    <span class="font-bold text-slate-900">{{ $trip->trip_number }}</span>
                    <span class="text-xs text-white px-2 py-0.5 rounded-full {{ $bg }}">{{ $trip->status?->getLabel() }}</span>
                </div>
                <div class="text-sm text-slate-700">{{ $trip->customer?->full_name }}</div>
                <div class="text-xs text-slate-500 mt-1">
                    {{ \Illuminate\Support\Carbon::parse($trip->scheduled_start)->format('H:i') }}
                    →
                    {{ \Illuminate\Support\Carbon::parse($trip->scheduled_end)->format('M j H:i') }}
                </div>
                <div class="text-xs text-slate-500 mt-1">{{ $trip->car?->plate }} — {{ $trip->car?->make }} {{ $trip->car?->model }}</div>
                <div class="text-xs text-slate-600 mt-1">📍 {{ $trip->pickup_location }} → {{ $trip->dropoff_location }}</div>
            </a>
        @empty
            <div class="bg-white rounded-xl p-6 text-center text-slate-500 border border-slate-200">
                {{ __('driver.no_trips_today') }}
            </div>
        @endforelse
    </section>

    @if($upcoming->isNotEmpty())
    <section>
        <h2 class="text-lg font-bold text-slate-900 mb-3">{{ __('driver.upcoming') }}</h2>
        @foreach($upcoming as $trip)
            <a href="{{ route('driver.trips.show', $trip->id) }}" class="block mb-2 bg-white rounded-xl p-3 shadow-sm border border-slate-200">
                <div class="flex items-center justify-between">
                    <span class="font-semibold text-sm text-slate-900">{{ $trip->trip_number }}</span>
                    <span class="text-xs text-slate-500">{{ \Illuminate\Support\Carbon::parse($trip->scheduled_start)->format('M j, H:i') }}</span>
                </div>
                <div class="text-xs text-slate-600 mt-0.5">{{ $trip->customer?->full_name }} — {{ $trip->car?->plate }}</div>
            </a>
        @endforeach
    </section>
    @endif
@endsection
