@extends('driver.layout')

@section('content')
    <a href="{{ route('driver.dashboard') }}" class="text-sm text-amber-600 mb-3 inline-block">← {{ __('driver.back') }}</a>

    <div class="bg-gradient-to-br from-emerald-500 to-emerald-700 text-white rounded-2xl p-5 shadow-md mb-4">
        <div class="text-xs opacity-90">{{ __('driver.net_payable_this_month') }}</div>
        <div class="text-3xl font-bold mt-1">EGP {{ number_format((float) $netPayable, 2) }}</div>
        <div class="text-xs opacity-90 mt-2">{{ $periodStart->format('Y-m-d') }} → {{ $periodEnd->format('Y-m-d') }}</div>
    </div>

    <div class="grid grid-cols-2 gap-3 mb-4">
        <div class="bg-white rounded-xl p-3 border border-slate-200">
            <div class="text-xs text-slate-500">{{ __('driver.gross_commission') }}</div>
            <div class="text-lg font-bold text-emerald-600 mt-1">EGP {{ number_format((float) $commissionTotal, 2) }}</div>
            <div class="text-xs text-slate-400 mt-0.5">{{ number_format((float) $commissionPct, 1) }}% × {{ __('driver.subtotal') }}</div>
        </div>
        <div class="bg-white rounded-xl p-3 border border-slate-200">
            <div class="text-xs text-slate-500">{{ __('driver.deductions') }}</div>
            <div class="text-lg font-bold text-rose-600 mt-1">EGP {{ number_format((float) $finesTotal, 2) }}</div>
            <div class="text-xs text-slate-400 mt-0.5">{{ count($fines) }} {{ __('driver.fines_count') }}</div>
        </div>
    </div>

    <section class="mb-4">
        <h2 class="font-bold text-slate-900 mb-2">{{ __('driver.trips_this_period') }} ({{ count($tripRows) }})</h2>
        <div class="bg-white rounded-xl border border-slate-200 overflow-hidden">
            @forelse($tripRows as $row)
                <div class="flex items-center justify-between px-3 py-2 border-b border-slate-100 last:border-0 text-sm">
                    <div>
                        <div class="font-semibold">{{ $row['trip_number'] }}</div>
                        <div class="text-xs text-slate-400">{{ $row['date'] }} · EGP {{ number_format((float) $row['subtotal'], 2) }}</div>
                    </div>
                    <div class="text-emerald-600 font-bold">+{{ number_format((float) $row['commission'], 2) }}</div>
                </div>
            @empty
                <div class="px-3 py-6 text-center text-slate-500 text-sm">{{ __('driver.no_trips_this_period') }}</div>
            @endforelse
        </div>
    </section>

    @if(count($fines) > 0)
        <section>
            <h2 class="font-bold text-slate-900 mb-2">{{ __('driver.fines_deducted') }}</h2>
            <div class="bg-white rounded-xl border border-slate-200 overflow-hidden">
                @foreach($fines as $fine)
                    <div class="flex items-center justify-between px-3 py-2 border-b border-slate-100 last:border-0 text-sm">
                        <div>
                            <div class="font-semibold">{{ $fine->violation_number }}</div>
                            <div class="text-xs text-slate-400">{{ \Illuminate\Support\Carbon::parse($fine->violation_date)->format('Y-m-d') }}</div>
                        </div>
                        <div class="text-rose-600 font-bold">−{{ number_format((float) $fine->amount, 2) }}</div>
                    </div>
                @endforeach
            </div>
        </section>
    @endif
@endsection
