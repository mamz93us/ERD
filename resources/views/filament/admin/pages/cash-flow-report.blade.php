<x-filament-panels::page>
    <form wire:submit.prevent>
        {{ $this->filtersForm }}
    </form>

    @php $netPositive = bccomp($totalNet, '0.00', 2) >= 0; @endphp

    <div style="margin-top: 1rem; padding: 12px 16px; background-color: #fef3c7; color: #78350f; border-radius: 8px; font-size: 13px;">
        {{ __('reports.period') }}: <strong>{{ $from->toDateString() }}</strong> → <strong>{{ $to->toDateString() }}</strong>
    </div>

    <div style="margin-top: 1rem; display: grid; grid-template-columns: 1fr 1fr 1fr; gap: 12px;">
        <div style="background-color: #ecfdf5; border: 1px solid #a7f3d0; border-radius: 12px; padding: 16px; text-align: center;">
            <div style="font-size: 12px; color: #065f46;">{{ __('reports.cash_in') }}</div>
            <div style="font-size: 22px; font-weight: 800; color: #059669; margin-top: 4px;">EGP {{ number_format((float) $totalIn, 2) }}</div>
        </div>
        <div style="background-color: #fef2f2; border: 1px solid #fecaca; border-radius: 12px; padding: 16px; text-align: center;">
            <div style="font-size: 12px; color: #7f1d1d;">{{ __('reports.cash_out') }}</div>
            <div style="font-size: 22px; font-weight: 800; color: #dc2626; margin-top: 4px;">EGP {{ number_format((float) $totalOut, 2) }}</div>
        </div>
        <div style="background-color: {{ $netPositive ? '#f0fdf4' : '#fef2f2' }}; border: 2px solid {{ $netPositive ? '#059669' : '#dc2626' }}; border-radius: 12px; padding: 16px; text-align: center;">
            <div style="font-size: 12px; color: {{ $netPositive ? '#14532d' : '#7f1d1d' }};">{{ __('reports.net_cash_flow') }}</div>
            <div style="font-size: 22px; font-weight: 800; color: {{ $netPositive ? '#059669' : '#dc2626' }}; margin-top: 4px;">EGP {{ number_format((float) $totalNet, 2) }}</div>
        </div>
    </div>

    <div style="margin-top: 1rem; overflow-x: auto; border-radius: 12px; border: 1px solid #e5e7eb; background-color: #ffffff; box-shadow: 0 1px 2px rgba(0,0,0,0.05);">
        <table style="min-width: 100%; border-collapse: collapse; font-size: 13px;">
            <thead style="background-color: #f3f4f6;">
                <tr>
                    <th style="padding: 12px 16px; text-align: start; font-weight: 700; color: #1f2937; border-bottom: 2px solid #d1d5db;">{{ __('reports.month') }}</th>
                    <th style="padding: 12px 16px; text-align: center; font-weight: 600; color: #059669; border-bottom: 2px solid #d1d5db;">{{ __('reports.cash_in') }}</th>
                    <th style="padding: 12px 16px; text-align: center; font-weight: 600; color: #dc2626; border-bottom: 2px solid #d1d5db;">{{ __('reports.cash_out') }}</th>
                    <th style="padding: 12px 16px; text-align: center; font-weight: 700; color: #111827; border-bottom: 2px solid #d1d5db; background-color: #f9fafb;">{{ __('reports.net') }}</th>
                </tr>
            </thead>
            <tbody>
                @foreach($months as $i => $row)
                    @php $netRowPositive = bccomp($row['net'], '0.00', 2) >= 0; @endphp
                    <tr style="background-color: {{ $i % 2 === 0 ? '#ffffff' : '#f9fafb' }};">
                        <td style="padding: 10px 16px; color: #111827; font-weight: 600; border-bottom: 1px solid #e5e7eb;">{{ $row['label'] }}</td>
                        <td style="padding: 10px 16px; text-align: center; color: #059669; border-bottom: 1px solid #e5e7eb;">EGP {{ number_format((float) $row['in'], 2) }}</td>
                        <td style="padding: 10px 16px; text-align: center; color: #dc2626; border-bottom: 1px solid #e5e7eb;">EGP {{ number_format((float) $row['out'], 2) }}</td>
                        <td style="padding: 10px 16px; text-align: center; color: {{ $netRowPositive ? '#059669' : '#dc2626' }}; font-weight: 700; border-bottom: 1px solid #e5e7eb; background-color: #f9fafb;">EGP {{ number_format((float) $row['net'], 2) }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</x-filament-panels::page>
