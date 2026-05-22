<?php

declare(strict_types=1);

namespace App\Filament\Admin\Pages;

use App\Models\Car;
use BackedEnum;
use Carbon\CarbonImmutable;
use Filament\Forms\Components\DatePicker;
use Filament\Pages\Page;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;

/**
 * Combined "Revenue per car" + "Per-car P&L" report.
 *
 * For each car in the period:
 *   revenue = sum(invoice_lines.line_total) where the line's trip is on this car
 *   driver_commission = sum(trip.subtotal * driver.trip_commission_percentage / 100)
 *   sub_rental_cost = days_used * sub_rental_contract.daily_cost  (only for sub-rented cars)
 *   expenses = sum(expenses.amount where car_id = this car)
 *   fines = sum(traffic_fines.amount where car_id = this car AND deducted_from_driver = false)
 *   profit = revenue − (commission + sub_rental + expenses + fines)
 *
 * Spec §9.5 defines this formula. Allocated overhead (rent/salaries) is NOT
 * included here — that goes in the overall P&L. This is direct attribution.
 */
class CarProfitabilityReport extends Page
{
    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedChartBar;

    protected string $view = 'filament.admin.pages.car-profitability-report';

    /** @var array{from: string, to: string} */
    public array $filters = ['from' => '', 'to' => ''];

    public function mount(): void
    {
        $this->filters['from'] = CarbonImmutable::today()->startOfMonth()->toDateString();
        $this->filters['to'] = CarbonImmutable::today()->endOfMonth()->toDateString();
    }

    public static function getNavigationLabel(): string
    {
        return __('navigation.car_profitability');
    }

    public static function getNavigationGroup(): ?string
    {
        return __('navigation.reports');
    }

    public function getTitle(): string
    {
        return __('navigation.car_profitability');
    }

    public function filtersForm(Schema $schema): Schema
    {
        return $schema
            ->statePath('filters')
            ->components([
                DatePicker::make('from')->label(__('reports.from'))->required()->live(),
                DatePicker::make('to')->label(__('reports.to'))->required()->live(),
            ]);
    }

    protected function getViewData(): array
    {
        $from = CarbonImmutable::parse($this->filters['from']);
        $to = CarbonImmutable::parse($this->filters['to']);
        $fromDate = $from->toDateString();
        $toDate = $to->toDateString();

        $cars = Car::query()
            ->with(['category:id,name,name_ar', 'branch:id,code'])
            ->orderBy('plate')
            ->get();

        $rows = [];
        foreach ($cars as $car) {
            $revenue = \DB::table('invoice_lines')
                ->join('trips', 'invoice_lines.trip_id', '=', 'trips.id')
                ->join('invoices', 'invoice_lines.invoice_id', '=', 'invoices.id')
                ->where('trips.car_id', $car->id)
                ->whereNotIn('invoices.status', ['cancelled', 'draft'])
                ->whereBetween('invoices.issue_date', [$fromDate, $toDate])
                ->whereNull('invoices.deleted_at')
                ->sum('invoice_lines.line_total');

            $commission = \DB::table('trips')
                ->join('drivers', 'trips.driver_id', '=', 'drivers.id')
                ->where('trips.car_id', $car->id)
                ->whereIn('trips.status', ['completed', 'invoiced', 'closed'])
                ->whereBetween('trips.scheduled_end', [$fromDate, $toDate])
                ->whereNull('trips.deleted_at')
                ->sum(\DB::raw('trips.subtotal * drivers.trip_commission_percentage / 100'));

            $expenses = \DB::table('expenses')
                ->where('car_id', $car->id)
                ->whereBetween('expense_date', [$fromDate, $toDate])
                ->whereNull('deleted_at')
                ->sum('amount');

            $fines = \DB::table('traffic_fines')
                ->where('car_id', $car->id)
                ->where('deducted_from_driver', false)
                ->whereBetween('violation_date', [$from->startOfDay(), $to->endOfDay()])
                ->sum('amount');

            $subRental = '0.00';
            if ($car->ownership_type?->value === 'sub_rented') {
                $contracts = \DB::table('sub_rental_contracts')
                    ->where('car_id', $car->id)
                    ->where('status', 'active')
                    ->where('start_date', '<=', $toDate)
                    ->where('end_date', '>=', $fromDate)
                    ->get(['start_date', 'end_date', 'daily_cost']);

                foreach ($contracts as $c) {
                    $effStart = max($c->start_date, $fromDate);
                    $effEnd = min($c->end_date, $toDate);
                    $days = CarbonImmutable::parse($effStart)->diffInDays(CarbonImmutable::parse($effEnd)) + 1;
                    $subRental = bcadd($subRental, bcmul((string) $days, (string) $c->daily_cost, 2), 2);
                }
            }

            $rev = (string) ($revenue ?? '0.00');
            $cost = bcadd(bcadd(bcadd((string) ($commission ?? '0.00'), (string) ($expenses ?? '0.00'), 2), (string) ($fines ?? '0.00'), 2), $subRental, 2);
            $profit = bcsub($rev, $cost, 2);

            if (bccomp($rev, '0.00', 2) === 0 && bccomp($cost, '0.00', 2) === 0) {
                continue;
            }

            $rows[] = [
                'plate' => $car->plate,
                'category' => $car->category?->name ?? '—',
                'branch' => $car->branch?->code ?? '—',
                'revenue' => $rev,
                'commission' => (string) ($commission ?? '0.00'),
                'sub_rental' => $subRental,
                'expenses' => (string) ($expenses ?? '0.00'),
                'fines' => (string) ($fines ?? '0.00'),
                'total_cost' => $cost,
                'profit' => $profit,
            ];
        }

        usort($rows, fn ($a, $b) => bccomp($b['profit'], $a['profit'], 2));

        $totals = [
            'revenue' => '0.00', 'commission' => '0.00', 'sub_rental' => '0.00',
            'expenses' => '0.00', 'fines' => '0.00', 'total_cost' => '0.00', 'profit' => '0.00',
        ];
        foreach ($rows as $r) {
            foreach ($totals as $k => $v) {
                $totals[$k] = bcadd($v, $r[$k], 2);
            }
        }

        return [
            'rows' => $rows,
            'totals' => $totals,
            'from' => $from,
            'to' => $to,
        ];
    }
}
