@extends('portal.layout')

@section('content')
<h1 class="text-xl font-bold mb-4">{{ __('portal.my_quotations') }}</h1>

<div class="bg-white rounded-2xl shadow-sm border border-slate-200 overflow-hidden">
    @forelse($quotations as $q)
        <a href="{{ route('portal.quotations.show', $q->id) }}" class="block px-4 py-3 border-b border-slate-100 last:border-0 hover:bg-slate-50">
            <div class="flex items-center justify-between">
                <div>
                    <div class="font-bold">{{ $q->quotation_number }}</div>
                    <div class="text-xs text-slate-500">{{ __('portal.valid_until') }} {{ \Illuminate\Support\Carbon::parse($q->valid_until)->format('Y-m-d') }}</div>
                </div>
                <div class="text-end">
                    <div class="font-bold text-emerald-600">EGP {{ number_format((float) $q->total_amount, 2) }}</div>
                    <span class="text-xs text-slate-500">{{ $q->status?->getLabel() }}</span>
                </div>
            </div>
        </a>
    @empty
        <div class="px-4 py-8 text-center text-slate-500">{{ __('portal.no_quotations_yet') }}</div>
    @endforelse
</div>

<div class="mt-4">{{ $quotations->links() }}</div>
@endsection
