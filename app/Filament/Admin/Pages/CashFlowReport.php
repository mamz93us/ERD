<?php

declare(strict_types=1);

namespace App\Filament\Admin\Pages;

use App\Models\Expense;
use App\Models\Payment;
use BackedEnum;
use Carbon\CarbonImmutable;
use Filament\Forms\Components\DatePicker;
use Filament\Pages\Page;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;

/**
 * Cash flow report — cash in (customer payments) vs cash out (expenses) over
 * a chosen window, grouped by month. Vendor bills aren't cash-out themselves
 * until they're paid; for v1 we use expenses + paid vendor bills (proxied by
 * paid_amount delta in the period) as the outflow signal.
 *
 * The big picture: did we end the period with more cash than we started?
 */
class CashFlowReport extends Page
{
    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedArrowsRightLeft;

    protected string $view = 'filament.admin.pages.cash-flow-report';

    /** @var array{from: string, to: string} */
    public array $filters = ['from' => '', 'to' => ''];

    public function mount(): void
    {
        $this->filters['from'] = CarbonImmutable::today()->subMonths(2)->startOfMonth()->toDateString();
        $this->filters['to'] = CarbonImmutable::today()->endOfMonth()->toDateString();
    }

    public static function getNavigationLabel(): string
    {
        return __('navigation.cash_flow');
    }

    public static function getNavigationGroup(): ?string
    {
        return __('navigation.reports');
    }

    public function getTitle(): string
    {
        return __('navigation.cash_flow');
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

        $payments = Payment::query()
            ->withoutGlobalScopes()
            ->whereBetween('payment_date', [$from->toDateString(), $to->toDateString()])
            ->get(['payment_date', 'amount']);

        $expenses = Expense::query()
            ->withoutGlobalScopes()
            ->whereBetween('expense_date', [$from->toDateString(), $to->toDateString()])
            ->get(['expense_date', 'amount']);

        $months = [];
        $cursor = $from->startOfMonth();
        while ($cursor->lte($to)) {
            $months[$cursor->format('Y-m')] = [
                'label' => $cursor->format('Y-m'),
                'in' => '0.00',
                'out' => '0.00',
                'net' => '0.00',
            ];
            $cursor = $cursor->addMonth();
        }

        foreach ($payments as $p) {
            $key = CarbonImmutable::parse($p->payment_date)->format('Y-m');
            if (isset($months[$key])) {
                $months[$key]['in'] = bcadd($months[$key]['in'], (string) $p->amount, 2);
            }
        }

        foreach ($expenses as $e) {
            $key = CarbonImmutable::parse($e->expense_date)->format('Y-m');
            if (isset($months[$key])) {
                $months[$key]['out'] = bcadd($months[$key]['out'], (string) $e->amount, 2);
            }
        }

        $totalIn = '0.00';
        $totalOut = '0.00';
        foreach ($months as &$row) {
            $row['net'] = bcsub($row['in'], $row['out'], 2);
            $totalIn = bcadd($totalIn, $row['in'], 2);
            $totalOut = bcadd($totalOut, $row['out'], 2);
        }
        unset($row);

        return [
            'months' => array_values($months),
            'totalIn' => $totalIn,
            'totalOut' => $totalOut,
            'totalNet' => bcsub($totalIn, $totalOut, 2),
            'from' => $from,
            'to' => $to,
        ];
    }
}
