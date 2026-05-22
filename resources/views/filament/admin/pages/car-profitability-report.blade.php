<x-filament-panels::page>
    <form wire:submit.prevent>
        {{ $this->filtersForm }}
    </form>

    <div style="margin-top: 1rem; padding: 12px 16px; background-color: #fef3c7; color: #78350f; border-radius: 8px; font-size: 13px;">
        {{ __('reports.period') }}: <strong>{{ $from->toDateString() }}</strong> → <strong>{{ $to->toDateString() }}</strong>
    </div>

    <div style="margin-top: 1rem; overflow-x: auto; border-radius: 12px; border: 1px solid #e5e7eb; background-color: #ffffff; box-shadow: 0 1px 2px rgba(0,0,0,0.05);">
        <table style="min-width: 100%; border-collapse: collapse; font-size: 12px;">
            <thead style="background-color: #f3f4f6;">
                <tr>
                    <th style="padding: 12px 12px; text-align: start; font-weight: 700; color: #1f2937; border-bottom: 2px solid #d1d5db;">{{ __('reports.car') }}</th>
                    <th style="padding: 12px 12px; text-align: start; font-weight: 600; color: #6b7280; border-bottom: 2px solid #d1d5db;">{{ __('reports.category') }}</th>
                    <th style="padding: 12px 12px; text-align: center; font-weight: 600; color: #6b7280; border-bottom: 2px solid #d1d5db;">{{ __('reports.branch') }}</th>
                    <th style="padding: 12px 12px; text-align: center; font-weight: 700; color: #059669; border-bottom: 2px solid #d1d5db;">{{ __('reports.revenue') }}</th>
                    <th style="padding: 12px 12px; text-align: center; font-weight: 600; color: #dc2626; border-bottom: 2px solid #d1d5db;">{{ __('reports.commission') }}</th>
                    <th style="padding: 12px 12px; text-align: center; font-weight: 600; color: #dc2626; border-bottom: 2px solid #d1d5db;">{{ __('reports.sub_rental_cost') }}</th>
                    <th style="padding: 12px 12px; text-align: center; font-weight: 600; color: #dc2626; border-bottom: 2px solid #d1d5db;">{{ __('reports.expenses') }}</th>
                    <th style="padding: 12px 12px; text-align: center; font-weight: 600; color: #dc2626; border-bottom: 2px solid #d1d5db;">{{ __('reports.fines') }}</th>
                    <th style="padding: 12px 12px; text-align: center; font-weight: 700; color: #111827; border-bottom: 2px solid #d1d5db; background-color: #f9fafb;">{{ __('reports.profit') }}</th>
                </tr>
            </thead>
            <tbody>
                @forelse($rows as $i => $row)
                    @php $profitable = bccomp($row['profit'], '0.00', 2) >= 0; @endphp
                    <tr style="background-color: {{ $i % 2 === 0 ? '#ffffff' : '#f9fafb' }};">
                        <td style="padding: 8px 12px; color: #111827; font-weight: 700; border-bottom: 1px solid #e5e7eb;">{{ $row['plate'] }}</td>
                        <td style="padding: 8px 12px; color: #4b5563; border-bottom: 1px solid #e5e7eb;">{{ $row['category'] }}</td>
                        <td style="padding: 8px 12px; text-align: center; border-bottom: 1px solid #e5e7eb;">
                            <span style="display: inline-block; background-color: #dbeafe; color: #1e40af; font-size: 10px; font-weight: 600; padding: 1px 6px; border-radius: 9999px;">{{ $row['branch'] }}</span>
                        </td>
                        <td style="padding: 8px 12px; text-align: center; color: #059669; font-weight: 700; border-bottom: 1px solid #e5e7eb;">{{ number_format((float) $row['revenue'], 0) }}</td>
                        <td style="padding: 8px 12px; text-align: center; color: #dc2626; border-bottom: 1px solid #e5e7eb;">{{ number_format((float) $row['commission'], 0) }}</td>
                        <td style="padding: 8px 12px; text-align: center; color: #dc2626; border-bottom: 1px solid #e5e7eb;">{{ number_format((float) $row['sub_rental'], 0) }}</td>
                        <td style="padding: 8px 12px; text-align: center; color: #dc2626; border-bottom: 1px solid #e5e7eb;">{{ number_format((float) $row['expenses'], 0) }}</td>
                        <td style="padding: 8px 12px; text-align: center; color: #dc2626; border-bottom: 1px solid #e5e7eb;">{{ number_format((float) $row['fines'], 0) }}</td>
                        <td style="padding: 8px 12px; text-align: center; color: {{ $profitable ? '#059669' : '#dc2626' }}; font-weight: 800; border-bottom: 1px solid #e5e7eb; background-color: #f9fafb;">EGP {{ number_format((float) $row['profit'], 2) }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="9" style="padding: 48px 16px; text-align: center; color: #6b7280;">{{ __('reports.no_car_activity') }}</td>
                    </tr>
                @endforelse
            </tbody>
            @if(count($rows) > 0)
                <tfoot>
                    <tr style="background-color: #1f2937; color: #ffffff; font-weight: 700;">
                        <td colspan="3" style="padding: 12px;">{{ __('reports.grand_total') }}</td>
                        <td style="padding: 12px; text-align: center;">{{ number_format((float) $totals['revenue'], 0) }}</td>
                        <td style="padding: 12px; text-align: center;">{{ number_format((float) $totals['commission'], 0) }}</td>
                        <td style="padding: 12px; text-align: center;">{{ number_format((float) $totals['sub_rental'], 0) }}</td>
                        <td style="padding: 12px; text-align: center;">{{ number_format((float) $totals['expenses'], 0) }}</td>
                        <td style="padding: 12px; text-align: center;">{{ number_format((float) $totals['fines'], 0) }}</td>
                        <td style="padding: 12px; text-align: center; background-color: #111827;">EGP {{ number_format((float) $totals['profit'], 2) }}</td>
                    </tr>
                </tfoot>
            @endif
        </table>
    </div>
</x-filament-panels::page>
