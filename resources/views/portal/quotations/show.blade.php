@extends('portal.layout')

@php
    $canDecide = in_array($quotation->status?->value, ['draft', 'sent'], true);
@endphp

@section('content')
<a href="{{ route('portal.quotations.index') }}" class="text-sm text-amber-600 mb-3 inline-block">← {{ __('portal.back_to_quotations') }}</a>

<div class="bg-white rounded-2xl shadow-sm p-5 border border-slate-200">
    <div class="flex items-center justify-between mb-3">
        <h1 class="text-xl font-bold">{{ $quotation->quotation_number }}</h1>
        <span class="text-xs text-white px-2 py-1 rounded-full bg-sky-500">{{ $quotation->status?->getLabel() }}</span>
    </div>

    <div class="grid grid-cols-2 gap-3 text-sm mb-4">
        <div><div class="text-xs text-slate-500">{{ __('portal.pickup') }}</div><div class="font-semibold">📍 {{ $quotation->pickup_location }}</div><div class="text-xs">{{ \Illuminate\Support\Carbon::parse($quotation->pickup_at)->format('Y-m-d H:i') }}</div></div>
        <div><div class="text-xs text-slate-500">{{ __('portal.dropoff') }}</div><div class="font-semibold">📍 {{ $quotation->dropoff_location }}</div><div class="text-xs">{{ \Illuminate\Support\Carbon::parse($quotation->dropoff_at)->format('Y-m-d H:i') }}</div></div>
        <div><div class="text-xs text-slate-500">{{ __('portal.car_category') }}</div><div class="font-semibold">{{ $quotation->category?->name ?? '—' }}</div></div>
        <div><div class="text-xs text-slate-500">{{ __('portal.distance') }}</div><div class="font-semibold">{{ number_format((int) ($quotation->estimated_distance_km ?? 0)) }} km</div></div>
    </div>

    <div class="border-t border-slate-200 pt-3">
        <div class="flex items-center justify-between text-sm py-1"><span class="text-slate-500">{{ __('portal.subtotal') }}</span><span>EGP {{ number_format((float) $quotation->subtotal, 2) }}</span></div>
        <div class="flex items-center justify-between text-sm py-1"><span class="text-slate-500">{{ __('portal.vat') }}</span><span>EGP {{ number_format((float) $quotation->vat_amount, 2) }}</span></div>
        <div class="flex items-center justify-between text-base font-bold py-2 border-t border-slate-200 mt-2"><span>{{ __('portal.total') }}</span><span>EGP {{ number_format((float) $quotation->total_amount, 2) }}</span></div>
    </div>

    @if($quotation->terms_and_conditions)
        <div class="mt-3 text-xs text-slate-600 bg-slate-50 border-l-2 border-amber-500 px-3 py-2 whitespace-pre-wrap">{{ $quotation->terms_and_conditions }}</div>
    @endif

    @if($canDecide)
        <div class="mt-4 grid grid-cols-2 gap-3">
            <form method="POST" action="{{ route('portal.quotations.decide', $quotation->id) }}">
                @csrf
                <input type="hidden" name="action" value="accept" />
                <button type="submit" class="w-full bg-emerald-500 hover:bg-emerald-600 text-white font-bold rounded-lg py-3">✓ {{ __('portal.accept') }}</button>
            </form>
            <form method="POST" action="{{ route('portal.quotations.decide', $quotation->id) }}" onsubmit="return confirm('{{ __('portal.reject_confirm') }}');">
                @csrf
                <input type="hidden" name="action" value="reject" />
                <button type="submit" class="w-full bg-rose-500 hover:bg-rose-600 text-white font-bold rounded-lg py-3">✗ {{ __('portal.reject') }}</button>
            </form>
        </div>
    @endif
</div>
@endsection
