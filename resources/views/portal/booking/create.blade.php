@extends('portal.layout')

@section('content')
<a href="{{ route('portal.dashboard') }}" class="text-sm text-amber-600 mb-3 inline-block">← {{ __('portal.back_to_dashboard') }}</a>

<div class="bg-white rounded-2xl shadow-sm p-5 border border-slate-200">
    <h1 class="text-xl font-bold mb-1">{{ __('portal.request_booking_title') }}</h1>
    <p class="text-sm text-slate-500 mb-4">{{ __('portal.request_booking_subtitle') }}</p>

    <form method="POST" action="{{ route('portal.booking.store') }}" class="space-y-4">
        @csrf

        <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
            <div>
                <label class="block text-sm font-semibold mb-1">{{ __('portal.pickup_location') }}</label>
                <input type="text" name="pickup_location" required value="{{ old('pickup_location') }}" class="w-full rounded-lg border border-slate-300 px-3 py-2" />
            </div>
            <div>
                <label class="block text-sm font-semibold mb-1">{{ __('portal.dropoff_location') }}</label>
                <input type="text" name="dropoff_location" required value="{{ old('dropoff_location') }}" class="w-full rounded-lg border border-slate-300 px-3 py-2" />
            </div>
            <div>
                <label class="block text-sm font-semibold mb-1">{{ __('portal.pickup_at') }}</label>
                <input type="datetime-local" name="pickup_at" required value="{{ old('pickup_at') }}" class="w-full rounded-lg border border-slate-300 px-3 py-2" />
            </div>
            <div>
                <label class="block text-sm font-semibold mb-1">{{ __('portal.dropoff_at') }}</label>
                <input type="datetime-local" name="dropoff_at" required value="{{ old('dropoff_at') }}" class="w-full rounded-lg border border-slate-300 px-3 py-2" />
            </div>
            <div class="md:col-span-2">
                <label class="block text-sm font-semibold mb-1">{{ __('portal.estimated_distance_km') }}</label>
                <input type="number" name="estimated_distance_km" min="0" value="{{ old('estimated_distance_km') }}" class="w-full rounded-lg border border-slate-300 px-3 py-2" />
            </div>
        </div>

        <div>
            <label class="block text-sm font-semibold mb-1">{{ __('portal.requirements') }}</label>
            <textarea name="requirements" rows="4" required class="w-full rounded-lg border border-slate-300 px-3 py-2">{{ old('requirements') }}</textarea>
            <p class="text-xs text-slate-500 mt-1">{{ __('portal.requirements_help') }}</p>
        </div>

        <button type="submit" class="w-full bg-amber-500 hover:bg-amber-600 text-white font-bold rounded-lg py-3 shadow-md">
            {{ __('portal.submit_booking') }}
        </button>
    </form>
</div>
@endsection
