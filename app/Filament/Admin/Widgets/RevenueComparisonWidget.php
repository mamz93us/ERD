<?php

declare(strict_types=1);

namespace App\Filament\Admin\Widgets;

use Carbon\CarbonImmutable;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

/**
 * Spec widget: this month's revenue vs last month, with delta.
 * Pulls invoices.total for issued (non-cancelled/draft) invoices issued
 * within the relevant month window.
 */
class RevenueComparisonWidget extends StatsOverviewWidget
{
    protected ?string $heading = null;

    protected static ?int $sort = 2;

    /** @return array<Stat> */
    protected function getStats(): array
    {
        $now = CarbonImmutable::now();
        $thisStart = $now->startOfMonth();
        $thisEnd = $now;
        $lastStart = $thisStart->subMonth();
        $lastEnd = $thisStart->subDay()->endOfDay();

        $thisRevenue = (string) \DB::table('invoices')
            ->whereNotIn('status', ['cancelled', 'draft'])
            ->whereBetween('issue_date', [$thisStart->toDateString(), $thisEnd->toDateString()])
            ->whereNull('deleted_at')
            ->sum('total');

        $lastRevenue = (string) \DB::table('invoices')
            ->whereNotIn('status', ['cancelled', 'draft'])
            ->whereBetween('issue_date', [$lastStart->toDateString(), $lastEnd->toDateString()])
            ->whereNull('deleted_at')
            ->sum('total');

        $delta = bcsub($thisRevenue, $lastRevenue, 2);
        $deltaPct = bccomp($lastRevenue, '0.00', 2) > 0
            ? round(((float) $delta / (float) $lastRevenue) * 100, 1)
            : 0.0;
        $up = bccomp($delta, '0.00', 2) >= 0;

        return [
            Stat::make(__('widgets.this_month_revenue'), 'EGP '.number_format((float) $thisRevenue, 2))
                ->description(($up ? '▲ +' : '▼ ').number_format($deltaPct, 1).'% '.__('widgets.vs_last_month'))
                ->color($up ? 'success' : 'danger'),
            Stat::make(__('widgets.last_month_revenue'), 'EGP '.number_format((float) $lastRevenue, 2))
                ->description($lastStart->format('Y-m'))
                ->color('gray'),
        ];
    }
}
