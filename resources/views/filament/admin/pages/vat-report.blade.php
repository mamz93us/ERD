<x-filament-panels::page>
    <form wire:submit.prevent>
        {{ $this->filtersForm }}
    </form>

    @php
        $netPositive = bccomp($net, '0.00', 2) >= 0;
    @endphp

    <div style="margin-top: 1rem; padding: 12px 16px; background-color: #fef3c7; color: #78350f; border-radius: 8px; font-size: 13px;">
        {{ __('reports.period') }}: <strong>{{ $from->toDateString() }}</strong> → <strong>{{ $to->toDateString() }}</strong>
    </div>

    <div style="margin-top: 1rem; display: grid; grid-template-columns: 1fr 1fr; gap: 16px;">
        {{-- Output VAT --}}
        <div style="background-color: #ffffff; border: 1px solid #e5e7eb; border-radius: 12px; box-shadow: 0 1px 2px rgba(0,0,0,0.05); padding: 16px;">
            <div style="font-weight: 700; color: #059669; font-size: 15px; margin-bottom: 12px;">
                {{ __('reports.output_vat_title') }}
                <span style="margin-{{ app()->getLocale() === 'ar' ? 'right' : 'left' }}: 8px; font-size: 11px; background-color: #d1fae5; color: #065f46; padding: 2px 8px; border-radius: 9999px;">{{ $output['count'] }}</span>
            </div>
            <table style="width: 100%; font-size: 13px;">
                <tr>
                    <td style="padding: 6px 0; color: #6b7280;">{{ __('reports.taxable_base') }}</td>
                    <td style="padding: 6px 0; text-align: end; color: #111827; font-weight: 600;">EGP {{ number_format((float) $output['taxable'], 2) }}</td>
                </tr>
                <tr>
                    <td style="padding: 6px 0; color: #6b7280;">{{ __('reports.vat_amount') }} (14%)</td>
                    <td style="padding: 6px 0; text-align: end; color: #059669; font-weight: 700;">EGP {{ number_format((float) $output['vat'], 2) }}</td>
                </tr>
                <tr>
                    <td style="padding: 8px 0; color: #111827; font-weight: 700; border-top: 1px solid #e5e7eb;">{{ __('reports.gross_total') }}</td>
                    <td style="padding: 8px 0; text-align: end; color: #111827; font-weight: 700; border-top: 1px solid #e5e7eb;">EGP {{ number_format((float) $output['total'], 2) }}</td>
                </tr>
            </table>
        </div>

        {{-- Input VAT --}}
        <div style="background-color: #ffffff; border: 1px solid #e5e7eb; border-radius: 12px; box-shadow: 0 1px 2px rgba(0,0,0,0.05); padding: 16px;">
            <div style="font-weight: 700; color: #0284c7; font-size: 15px; margin-bottom: 12px;">
                {{ __('reports.input_vat_title') }}
                <span style="margin-{{ app()->getLocale() === 'ar' ? 'right' : 'left' }}: 8px; font-size: 11px; background-color: #dbeafe; color: #1e40af; padding: 2px 8px; border-radius: 9999px;">{{ $input['count'] }}</span>
            </div>
            <table style="width: 100%; font-size: 13px;">
                <tr>
                    <td style="padding: 6px 0; color: #6b7280;">{{ __('reports.taxable_base') }}</td>
                    <td style="padding: 6px 0; text-align: end; color: #111827; font-weight: 600;">EGP {{ number_format((float) $input['taxable'], 2) }}</td>
                </tr>
                <tr>
                    <td style="padding: 6px 0; color: #6b7280;">{{ __('reports.vat_amount') }}</td>
                    <td style="padding: 6px 0; text-align: end; color: #0284c7; font-weight: 700;">EGP {{ number_format((float) $input['vat'], 2) }}</td>
                </tr>
                <tr>
                    <td style="padding: 8px 0; color: #111827; font-weight: 700; border-top: 1px solid #e5e7eb;">{{ __('reports.gross_total') }}</td>
                    <td style="padding: 8px 0; text-align: end; color: #111827; font-weight: 700; border-top: 1px solid #e5e7eb;">EGP {{ number_format((float) $input['total'], 2) }}</td>
                </tr>
            </table>
        </div>
    </div>

    {{-- Net --}}
    <div style="margin-top: 16px; background-color: {{ $netPositive ? '#fef2f2' : '#f0fdf4' }}; border: 2px solid {{ $netPositive ? '#dc2626' : '#059669' }}; border-radius: 12px; padding: 20px; text-align: center;">
        <div style="font-size: 13px; color: {{ $netPositive ? '#7f1d1d' : '#14532d' }}; margin-bottom: 6px;">
            {{ $netPositive ? __('reports.net_vat_payable') : __('reports.net_vat_refundable') }}
        </div>
        <div style="font-size: 26px; font-weight: 800; color: {{ $netPositive ? '#dc2626' : '#059669' }};">
            EGP {{ number_format(abs((float) $net), 2) }}
        </div>
        <div style="margin-top: 6px; font-size: 11px; color: #6b7280;">
            {{ __('reports.net_vat_formula') }}
        </div>
    </div>
</x-filament-panels::page>
