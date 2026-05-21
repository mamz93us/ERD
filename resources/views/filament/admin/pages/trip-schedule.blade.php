<x-filament-panels::page>
    <form wire:submit.prevent>
        {{ $this->filtersForm }}
    </form>

    @php
        /** @var \Illuminate\Database\Eloquent\Collection<int, \App\Models\Car> $cars */
        /** @var list<\Carbon\CarbonImmutable> $days */
        /** @var array<string, array<string, list<\App\Models\Trip>>> $tripsByCarAndDay */
    @endphp

    <div class="mt-4 overflow-x-auto rounded-xl border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-900">
        <table class="min-w-full text-xs border-collapse">
            <thead class="bg-gray-50 dark:bg-gray-800 sticky top-0 z-10">
                <tr>
                    <th class="sticky left-0 z-20 bg-gray-50 dark:bg-gray-800 border-b border-r border-gray-200 dark:border-gray-700 px-3 py-2 text-start font-semibold text-gray-700 dark:text-gray-300 min-w-[140px]">
                        {{ __('trip_schedule.car') }}
                    </th>
                    @foreach($days as $day)
                        <th class="border-b border-r border-gray-200 dark:border-gray-700 px-2 py-2 text-center font-medium text-gray-600 dark:text-gray-400 min-w-[110px] {{ $day->isWeekend() ? 'bg-amber-50/40 dark:bg-amber-900/10' : '' }} {{ $day->isToday() ? 'bg-primary-50 dark:bg-primary-900/30 text-primary-700 dark:text-primary-200 font-bold' : '' }}">
                            <div>{{ $day->format('D') }}</div>
                            <div class="text-xs">{{ $day->format('M j') }}</div>
                        </th>
                    @endforeach
                </tr>
            </thead>
            <tbody>
                @forelse($cars as $car)
                    <tr class="hover:bg-gray-50 dark:hover:bg-gray-800">
                        <td class="sticky left-0 z-10 bg-white dark:bg-gray-900 border-b border-r border-gray-200 dark:border-gray-700 px-3 py-2 font-medium">
                            <div class="text-gray-900 dark:text-gray-100">{{ $car->plate }}</div>
                            <div class="text-xs text-gray-500 dark:text-gray-400">{{ $car->make }} {{ $car->model }}</div>
                            <div class="text-xs text-gray-400">{{ $car->branch?->code }}</div>
                        </td>
                        @foreach($days as $day)
                            @php $tripsToday = $tripsByCarAndDay[$car->id][$day->toDateString()] ?? []; @endphp
                            <td class="border-b border-r border-gray-200 dark:border-gray-700 align-top p-1 {{ $day->isWeekend() ? 'bg-amber-50/30 dark:bg-amber-900/5' : '' }}">
                                @foreach($tripsToday as $trip)
                                    @php
                                        $color = match($trip->status?->value) {
                                            'confirmed', 'assigned' => 'bg-sky-100 text-sky-900 border-sky-300 dark:bg-sky-900/40 dark:text-sky-100 dark:border-sky-700',
                                            'en_route', 'in_progress' => 'bg-amber-100 text-amber-900 border-amber-300 dark:bg-amber-900/40 dark:text-amber-100 dark:border-amber-700',
                                            'completed', 'invoiced', 'closed' => 'bg-emerald-100 text-emerald-900 border-emerald-300 dark:bg-emerald-900/40 dark:text-emerald-100 dark:border-emerald-700',
                                            'cancelled', 'no_show' => 'bg-rose-100 text-rose-900 border-rose-300 dark:bg-rose-900/40 dark:text-rose-100 dark:border-rose-700',
                                            default => 'bg-gray-100 text-gray-900 border-gray-300 dark:bg-gray-700 dark:text-gray-100 dark:border-gray-600',
                                        };
                                    @endphp
                                    <a href="{{ route('filament.admin.resources.trips.edit', $trip) }}"
                                       class="block mb-1 px-2 py-1 rounded border-s-4 {{ $color }} hover:opacity-80 transition">
                                        <div class="font-semibold truncate">{{ $trip->trip_number }}</div>
                                        <div class="truncate opacity-80">{{ $trip->customer?->full_name }}</div>
                                        <div class="text-[10px] opacity-70">
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
                        <td colspan="{{ count($days) + 1 }}" class="px-4 py-8 text-center text-gray-500 dark:text-gray-400">
                            {{ __('trip_schedule.no_cars') }}
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-4 flex flex-wrap gap-3 text-xs text-gray-600 dark:text-gray-400">
        <span class="inline-flex items-center gap-1"><span class="inline-block h-3 w-3 rounded border-2 border-sky-400 bg-sky-100 dark:bg-sky-900/40"></span> {{ __('trip_schedule.legend_booked') }}</span>
        <span class="inline-flex items-center gap-1"><span class="inline-block h-3 w-3 rounded border-2 border-amber-400 bg-amber-100 dark:bg-amber-900/40"></span> {{ __('trip_schedule.legend_active') }}</span>
        <span class="inline-flex items-center gap-1"><span class="inline-block h-3 w-3 rounded border-2 border-emerald-400 bg-emerald-100 dark:bg-emerald-900/40"></span> {{ __('trip_schedule.legend_completed') }}</span>
        <span class="inline-flex items-center gap-1"><span class="inline-block h-3 w-3 rounded border-2 border-rose-400 bg-rose-100 dark:bg-rose-900/40"></span> {{ __('trip_schedule.legend_cancelled') }}</span>
    </div>
</x-filament-panels::page>
