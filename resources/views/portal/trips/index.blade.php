@extends('portal.layout')

@section('content')
<h1 class="text-xl font-bold mb-4">{{ __('portal.my_trips') }}</h1>

<div class="bg-white rounded-2xl shadow-sm border border-slate-200 overflow-hidden">
    @forelse($trips as $trip)
        @php
            $bg = match($trip->status?->value) {
                'in_progress','en_route' => 'bg-orange-500',
                'completed','invoiced','closed' => 'bg-emerald-500',
                'cancelled','no_show' => 'bg-rose-500',
                default => 'bg-sky-500',
            };
        @endphp
        <a href="{{ route('portal.trips.show', $trip->id) }}" class="block px-4 py-3 border-b border-slate-100 last:border-0 hover:bg-slate-50">
            <div class="flex items-center justify-between">
                <div>
                    <div class="font-bold">{{ $trip->trip_number }}</div>
                    <div class="text-xs text-slate-500">{{ $trip->car?->plate }} — {{ $trip->driver?->full_name }}</div>
                </div>
                <span class="text-xs text-white px-2 py-1 rounded-full {{ $bg }}">{{ $trip->status?->getLabel() }}</span>
            </div>
            <div class="text-xs text-slate-600 mt-1">📍 {{ $trip->pickup_location }} → {{ $trip->dropoff_location }}</div>
            <div class="text-xs text-slate-500 mt-1">{{ \Illuminate\Support\Carbon::parse($trip->scheduled_start)->format('Y-m-d H:i') }}</div>
        </a>
    @empty
        <div class="px-4 py-8 text-center text-slate-500">{{ __('portal.no_trips_yet') }}</div>
    @endforelse
</div>

<div class="mt-4">{{ $trips->links() }}</div>
@endsection
