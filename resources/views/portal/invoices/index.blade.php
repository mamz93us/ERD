@extends('portal.layout')

@section('content')
<h1 class="text-xl font-bold mb-4">{{ __('portal.my_invoices') }}</h1>

<div class="bg-white rounded-2xl shadow-sm border border-slate-200 overflow-hidden">
    @forelse($invoices as $inv)
        @php
            $color = match($inv->status?->value) {
                'paid' => 'bg-emerald-500',
                'partially_paid' => 'bg-amber-500',
                'overdue' => 'bg-rose-500',
                'cancelled' => 'bg-gray-500',
                default => 'bg-sky-500',
            };
        @endphp
        <div class="px-4 py-3 border-b border-slate-100 last:border-0 flex items-center justify-between">
            <div>
                <div class="font-bold">{{ $inv->invoice_number }}</div>
                <div class="text-xs text-slate-500">{{ __('portal.issued') }} {{ \Illuminate\Support\Carbon::parse($inv->issue_date)->format('Y-m-d') }} · {{ __('portal.due') }} {{ \Illuminate\Support\Carbon::parse($inv->due_date)->format('Y-m-d') }}</div>
                <span class="text-xs text-white px-2 py-0.5 rounded-full {{ $color }} inline-block mt-1">{{ $inv->status?->getLabel() }}</span>
            </div>
            <div class="text-end">
                <div class="font-bold">EGP {{ number_format((float) $inv->total, 2) }}</div>
                @if(bccomp((string) $inv->balance_due, '0.00', 2) > 0)
                    <div class="text-xs text-rose-600">{{ __('portal.balance') }}: {{ number_format((float) $inv->balance_due, 2) }}</div>
                @endif
                <a href="{{ route('portal.invoices.pdf', $inv->id) }}" class="text-xs text-amber-600 mt-1 inline-block">⬇ PDF</a>
            </div>
        </div>
    @empty
        <div class="px-4 py-8 text-center text-slate-500">{{ __('portal.no_invoices_yet') }}</div>
    @endforelse
</div>

<div class="mt-4">{{ $invoices->links() }}</div>
@endsection
