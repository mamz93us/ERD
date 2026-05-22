<x-filament-panels::page>
    <form wire:submit.prevent>
        {{ $this->filtersForm }}
    </form>

    @php
        /** @var \Illuminate\Support\Collection $rows */
        /** @var array<string,string> $totals */
        /** @var \Carbon\CarbonImmutable $asOf */
    @endphp

    <div style="margin-top: 1rem; padding: 12px 16px; background-color: #fef3c7; color: #78350f; border-radius: 8px; font-size: 13px;">
        {{ __('reports.as_of_label') }}: <strong>{{ $asOf->toDateString() }}</strong>
    </div>

    <div style="margin-top: 1rem; overflow-x: auto; border-radius: 12px; border: 1px solid #e5e7eb; background-color: #ffffff; box-shadow: 0 1px 2px rgba(0,0,0,0.05);">
        <table style="min-width: 100%; border-collapse: collapse; font-size: 13px;">
            <thead style="background-color: #f3f4f6;">
                <tr>
                    <th style="padding: 12px 16px; text-align: start; font-weight: 700; color: #1f2937; border-bottom: 2px solid #d1d5db;">
                        {{ __('reports.customer') }}
                    </th>
                    <th style="padding: 12px 16px; text-align: center; font-weight: 600; color: #059669; border-bottom: 2px solid #d1d5db;">
                        {{ __('reports.bucket_0_30') }}
                    </th>
                    <th style="padding: 12px 16px; text-align: center; font-weight: 600; color: #f59e0b; border-bottom: 2px solid #d1d5db;">
                        {{ __('reports.bucket_31_60') }}
                    </th>
                    <th style="padding: 12px 16px; text-align: center; font-weight: 600; color: #f97316; border-bottom: 2px solid #d1d5db;">
                        {{ __('reports.bucket_61_90') }}
                    </th>
                    <th style="padding: 12px 16px; text-align: center; font-weight: 600; color: #dc2626; border-bottom: 2px solid #d1d5db;">
                        {{ __('reports.bucket_90_plus') }}
                    </th>
                    <th style="padding: 12px 16px; text-align: center; font-weight: 700; color: #111827; border-bottom: 2px solid #d1d5db; background-color: #f9fafb;">
                        {{ __('reports.total') }}
                    </th>
                </tr>
            </thead>
            <tbody>
                @forelse($rows as $i => $row)
                    <tr style="background-color: {{ $i % 2 === 0 ? '#ffffff' : '#f9fafb' }};">
                        <td style="padding: 10px 16px; color: #111827; font-weight: 600; border-bottom: 1px solid #e5e7eb;">
                            {{ $row['name'] }}
                            @if($row['type'] === 'corporate')
                                <span style="display: inline-block; margin-{{ app()->getLocale() === 'ar' ? 'right' : 'left' }}: 6px; background-color: #dbeafe; color: #1e40af; font-size: 10px; font-weight: 600; padding: 1px 6px; border-radius: 9999px;">{{ __('reports.corporate') }}</span>
                            @endif
                        </td>
                        <td style="padding: 10px 16px; text-align: center; color: #059669; border-bottom: 1px solid #e5e7eb;">
                            EGP {{ number_format((float) $row['d0_30'], 2) }}
                        </td>
                        <td style="padding: 10px 16px; text-align: center; color: #f59e0b; border-bottom: 1px solid #e5e7eb;">
                            EGP {{ number_format((float) $row['d31_60'], 2) }}
                        </td>
                        <td style="padding: 10px 16px; text-align: center; color: #f97316; border-bottom: 1px solid #e5e7eb;">
                            EGP {{ number_format((float) $row['d61_90'], 2) }}
                        </td>
                        <td style="padding: 10px 16px; text-align: center; color: #dc2626; border-bottom: 1px solid #e5e7eb;">
                            EGP {{ number_format((float) $row['d90_plus'], 2) }}
                        </td>
                        <td style="padding: 10px 16px; text-align: center; color: #111827; font-weight: 700; border-bottom: 1px solid #e5e7eb; background-color: #f9fafb;">
                            EGP {{ number_format((float) $row['total'], 2) }}
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" style="padding: 48px 16px; text-align: center; color: #6b7280;">
                            {{ __('reports.no_outstanding') }}
                        </td>
                    </tr>
                @endforelse
            </tbody>
            @if($rows->isNotEmpty())
                <tfoot>
                    <tr style="background-color: #1f2937; color: #ffffff; font-weight: 700;">
                        <td style="padding: 12px 16px;">{{ __('reports.grand_total') }}</td>
                        <td style="padding: 12px 16px; text-align: center;">EGP {{ number_format((float) $totals['d0_30'], 2) }}</td>
                        <td style="padding: 12px 16px; text-align: center;">EGP {{ number_format((float) $totals['d31_60'], 2) }}</td>
                        <td style="padding: 12px 16px; text-align: center;">EGP {{ number_format((float) $totals['d61_90'], 2) }}</td>
                        <td style="padding: 12px 16px; text-align: center;">EGP {{ number_format((float) $totals['d90_plus'], 2) }}</td>
                        <td style="padding: 12px 16px; text-align: center; background-color: #111827;">EGP {{ number_format((float) $totals['total'], 2) }}</td>
                    </tr>
                </tfoot>
            @endif
        </table>
    </div>
</x-filament-panels::page>
