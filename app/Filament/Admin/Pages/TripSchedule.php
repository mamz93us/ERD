<?php

declare(strict_types=1);

namespace App\Filament\Admin\Pages;

use App\Models\Branch;
use App\Models\Car;
use App\Models\Trip;
use BackedEnum;
use Carbon\CarbonImmutable;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Toggle;
use Filament\Pages\Page;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Illuminate\Database\Eloquent\Collection;

/**
 * Read-only Cars × Days grid showing every trip in the window. Trips that span
 * multiple days render in every day they touch so the operator can see overlap
 * at a glance. Click a badge → opens that trip's edit page.
 *
 * Booking overlap PREVENTION lives in BookingAvailabilityService + MariaDB
 * triggers (Phase 5). This page is the VISIBILITY half — see what's already
 * booked before clicking "+ New Trip".
 */
class TripSchedule extends Page
{
    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedCalendarDays;

    protected string $view = 'filament.admin.pages.trip-schedule';

    /** @var array{days: int, branch_id: string|null, include_cancelled: bool} */
    public array $filters = [
        'days' => 14,
        'branch_id' => null,
        'include_cancelled' => false,
    ];

    public static function getNavigationLabel(): string
    {
        return __('navigation.trip_schedule');
    }

    public static function getNavigationGroup(): ?string
    {
        return __('navigation.operations');
    }

    public function getTitle(): string
    {
        return __('navigation.trip_schedule');
    }

    public function filtersForm(Schema $schema): Schema
    {
        return $schema
            ->statePath('filters')
            ->components([
                Select::make('days')
                    ->label(__('trip_schedule.window'))
                    ->options([
                        7 => __('trip_schedule.window_7'),
                        14 => __('trip_schedule.window_14'),
                        30 => __('trip_schedule.window_30'),
                    ])
                    ->default(14)
                    ->live(),
                Select::make('branch_id')
                    ->label(__('trip_schedule.branch'))
                    ->options(fn () => Branch::query()->pluck('code', 'id'))
                    ->placeholder(__('trip_schedule.all_branches'))
                    ->nullable()
                    ->live(),
                Toggle::make('include_cancelled')
                    ->label(__('trip_schedule.include_cancelled'))
                    ->live(),
            ]);
    }

    /**
     * @return array{
     *     days: list<CarbonImmutable>,
     *     cars: Collection<int, Car>,
     *     tripsByCarAndDay: array<string, array<string, list<Trip>>>,
     * }
     */
    protected function getViewData(): array
    {
        $days = (int) ($this->filters['days'] ?? 14);
        $branchId = $this->filters['branch_id'] ?? null;
        $includeCancelled = (bool) ($this->filters['include_cancelled'] ?? false);

        $start = CarbonImmutable::today();
        $end = $start->addDays($days - 1);

        $daysList = [];
        for ($i = 0; $i < $days; $i++) {
            $daysList[] = $start->addDays($i);
        }

        $cars = Car::query()
            ->when($branchId, fn ($q) => $q->where('branch_id', $branchId))
            ->orderBy('plate')
            ->get();

        $trips = Trip::query()
            ->withoutGlobalScopes()
            ->with(['customer:id,full_name', 'driver:id,full_name'])
            ->when($branchId, fn ($q) => $q->where('branch_id', $branchId))
            ->when(! $includeCancelled, fn ($q) => $q->whereNotIn('status', ['cancelled', 'no_show']))
            ->where('scheduled_start', '<=', $end->endOfDay())
            ->where('scheduled_end', '>=', $start->startOfDay())
            ->get();

        $tripsByCarAndDay = [];
        foreach ($trips as $trip) {
            $tripStart = CarbonImmutable::parse($trip->scheduled_start);
            $tripEnd = CarbonImmutable::parse($trip->scheduled_end);
            foreach ($daysList as $day) {
                if ($day->isBetween($tripStart->startOfDay(), $tripEnd->endOfDay())
                    || $day->toDateString() === $tripStart->toDateString()
                    || $day->toDateString() === $tripEnd->toDateString()) {
                    $tripsByCarAndDay[$trip->car_id][$day->toDateString()][] = $trip;
                }
            }
        }

        return [
            'days' => $daysList,
            'cars' => $cars,
            'tripsByCarAndDay' => $tripsByCarAndDay,
        ];
    }
}
