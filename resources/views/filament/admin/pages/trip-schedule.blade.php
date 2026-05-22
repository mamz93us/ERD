<x-filament-panels::page>
    <form wire:submit.prevent>
        {{ $this->filtersForm }}
    </form>

    @php
        /** @var \Illuminate\Database\Eloquent\Collection<int, \App\Models\Car> $cars */
        /** @var list<\Carbon\CarbonImmutable> $days */
        /** @var array<string, array<string, list<\App\Models\Trip>>> $tripsByCarAndDay */
    @endphp

    <div style="margin-top: 1rem; overflow-x: auto; border-radius: 12px; border: 1px solid #e5e7eb; background-color: #ffffff; box-shadow: 0 1px 2px rgba(0,0,0,0.05);">
        <table style="min-width: 100%; border-collapse: collapse; font-size: 12px;">
            <thead style="position: sticky; top: 0; z-index: 10; background-color: #f3f4f6;">
                <tr>
                    <th style="position: sticky; inset-inline-start: 0; z-index: 20; background-color: #f3f4f6; color: #1f2937; border-bottom: 2px solid #d1d5db; border-inline-end: 1px solid #d1d5db; padding: 12px 16px; text-align: start; font-weight: 700; min-width: 180px;">
                        {{ __('trip_schedule.car') }}
                    </th>
                    @foreach($days as $day)
                        @php
                            if ($day->isToday()) {
                                $headerStyle = 'background-color: #f59e0b; color: #ffffff;';
                            } elseif ($day->isWeekend()) {
                                $headerStyle = 'background-color: #fef3c7; color: #78350f;';
                            } else {
                                $headerStyle = 'background-color: #f3f4f6; color: #374151;';
                            }
                        @endphp
                        <th style="{{ $headerStyle }} padding: 12px 8px; text-align: center; font-weight: 600; min-width: 160px; border-bottom: 2px solid #d1d5db; border-inline-end: 1px solid #d1d5db;">
                            <div style="font-size: 11px; opacity: 0.9;">{{ $day->format('D') }}</div>
                            <div style="font-size: 14px; font-weight: 700;">{{ $day->format('M j') }}</div>
                        </th>
                    @endforeach
                </tr>
            </thead>
            <tbody>
                @forelse($cars as $car)
                    @php $rowEven = $loop->index % 2 === 0; @endphp
                    @php $rowBg = $rowEven ? '#ffffff' : '#f9fafb'; @endphp
                    <tr style="background-color: {{ $rowBg }};">
                        <td style="position: sticky; inset-inline-start: 0; z-index: 5; background-color: {{ $rowBg }}; padding: 12px 16px; border-bottom: 1px solid #e5e7eb; border-inline-end: 1px solid #e5e7eb; vertical-align: top;">
                            <div style="font-size: 14px; font-weight: 700; color: #111827;">{{ $car->plate }}</div>
                            <div style="font-size: 11px; color: #4b5563; margin-top: 2px;">{{ $car->make }} {{ $car->model }}</div>
                            <div style="margin-top: 6px;">
                                <span style="display: inline-block; background-color: #dbeafe; color: #1e40af; font-size: 10px; font-weight: 600; padding: 2px 8px; border-radius: 9999px;">
                                    {{ $car->branch?->code }}
                                </span>
                            </div>
                        </td>
                        @foreach($days as $day)
                            @php $tripsToday = $tripsByCarAndDay[$car->id][$day->toDateString()] ?? []; @endphp
                            @php
                                if ($day->isToday()) {
                                    $cellStyle = 'background-color: #fffbeb;';
                                } elseif ($day->isWeekend()) {
                                    $cellStyle = 'background-color: #fefce8;';
                                } else {
                                    $cellStyle = '';
                                }
                            @endphp
                            <td style="{{ $cellStyle }} vertical-align: top; padding: 6px; border-bottom: 1px solid #e5e7eb; border-inline-end: 1px solid #e5e7eb; min-height: 80px;">
                                @foreach($tripsToday as $trip)
                                    @php
                                        $bg = match($trip->status?->value) {
                                            'draft' => '#64748b',
                                            'confirmed', 'assigned' => '#0284c7',
                                            'en_route', 'in_progress' => '#f97316',
                                            'completed', 'invoiced', 'closed' => '#059669',
                                            'cancelled', 'no_show' => '#e11d48',
                                            default => '#6b7280',
                                        };
                                        $label = match($trip->status?->value) {
                                            'draft' => __('trip_schedule.status_draft'),
                                            'confirmed' => __('trip_schedule.status_confirmed'),
                                            'assigned' => __('trip_schedule.status_assigned'),
                                            'en_route' => __('trip_schedule.status_en_route'),
                                            'in_progress' => __('trip_schedule.status_in_progress'),
                                            'completed' => __('trip_schedule.status_completed'),
                                            'invoiced' => __('trip_schedule.status_invoiced'),
                                            'closed' => __('trip_schedule.status_closed'),
                                            'cancelled' => __('trip_schedule.status_cancelled'),
                                            'no_show' => __('trip_schedule.status_no_show'),
                                            default => '',
                                        };
                                    @endphp
                                    <a href="{{ route('filament.admin.resources.trips.edit', $trip) }}"
                                       style="display: block; margin-bottom: 6px; background-color: {{ $bg }}; color: #ffffff; padding: 8px 10px; border-radius: 8px; text-decoration: none; box-shadow: 0 1px 3px rgba(0,0,0,0.15); border: 1px solid rgba(255,255,255,0.15); min-height: 64px;">
                                        <div style="display: flex; align-items: center; justify-content: space-between; gap: 4px; margin-bottom: 2px;">
                                            <span style="font-size: 11px; font-weight: 700; color: #ffffff; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">{{ $trip->trip_number }}</span>
                                            @if($label)
                                                <span style="font-size: 9px; background-color: rgba(255,255,255,0.25); color: #ffffff; padding: 1px 6px; border-radius: 9999px; white-space: nowrap;">{{ $label }}</span>
                                            @endif
                                        </div>
                                        <div style="font-size: 11px; color: #ffffff; opacity: 0.95; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">{{ $trip->customer?->full_name }}</div>
                                        <div style="font-size: 10px; color: #ffffff; opacity: 0.9; margin-top: 4px;">
                                            {{ \Illuminate\Support\Carbon::parse($trip->scheduled_start)->format('H:i') }}
                                            →
                                            {{ \Illuminate\Support\Carbon::parse($trip->scheduled_end)->format('M j H:i') }}
                                        </div>
                                    </a>
                                @endforeach
                            </td>
                        @endforeach
                    </tr>
                @empty
                    <tr>
                        <td colspan="{{ count($days) + 1 }}" style="padding: 48px 16px; text-align: center; color: #6b7280;">
                            {{ __('trip_schedule.no_cars') }}
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div style="margin-top: 1rem; display: flex; flex-wrap: wrap; gap: 12px; font-size: 12px;">
        <span style="display: inline-flex; align-items: center; gap: 8px; background-color: #0284c7; color: #ffffff; padding: 6px 12px; border-radius: 9999px;">
            <span style="display: inline-block; background-color: #ffffff; height: 8px; width: 8px; border-radius: 9999px;"></span>
            {{ __('trip_schedule.legend_booked') }}
        </span>
        <span style="display: inline-flex; align-items: center; gap: 8px; background-color: #f97316; color: #ffffff; padding: 6px 12px; border-radius: 9999px;">
            <span style="display: inline-block; background-color: #ffffff; height: 8px; width: 8px; border-radius: 9999px;"></span>
            {{ __('trip_schedule.legend_active') }}
        </span>
        <span style="display: inline-flex; align-items: center; gap: 8px; background-color: #059669; color: #ffffff; padding: 6px 12px; border-radius: 9999px;">
            <span style="display: inline-block; background-color: #ffffff; height: 8px; width: 8px; border-radius: 9999px;"></span>
            {{ __('trip_schedule.legend_completed') }}
        </span>
        <span style="display: inline-flex; align-items: center; gap: 8px; background-color: #e11d48; color: #ffffff; padding: 6px 12px; border-radius: 9999px;">
            <span style="display: inline-block; background-color: #ffffff; height: 8px; width: 8px; border-radius: 9999px;"></span>
            {{ __('trip_schedule.legend_cancelled') }}
        </span>
    </div>
</x-filament-panels::page>
