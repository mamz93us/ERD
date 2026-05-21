<?php

declare(strict_types=1);

namespace App\Filament\Admin\Widgets;

use App\Models\CarDocument;
use App\Models\DriverDocument;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Database\Eloquent\Builder;

/**
 * Traffic-light coloring per spec §6 Phase 3.
 * Combines car_documents and driver_documents that are is_active=true with an expiry_date.
 */
class ExpiringDocumentsWidget extends StatsOverviewWidget
{
    protected ?string $heading = null;

    /** @return array<Stat> */
    protected function getStats(): array
    {
        $today = now()->startOfDay()->toDateString();
        $in30 = now()->startOfDay()->addDays(30)->toDateString();
        $in60 = now()->startOfDay()->addDays(60)->toDateString();

        $expired = $this->base(CarDocument::query())->where('expiry_date', '<', $today)->count()
            + $this->base(DriverDocument::query())->where('expiry_date', '<', $today)->count();

        $within30 = $this->base(CarDocument::query())->whereBetween('expiry_date', [$today, $in30])->count()
            + $this->base(DriverDocument::query())->whereBetween('expiry_date', [$today, $in30])->count();

        $within60 = $this->base(CarDocument::query())->whereBetween('expiry_date', [$today, $in60])->count()
            + $this->base(DriverDocument::query())->whereBetween('expiry_date', [$today, $in60])->count();

        return [
            Stat::make(__('widgets.expiring_documents.expired'), (string) $expired)
                ->description(__('widgets.expiring_documents.expired_description'))
                ->color($expired > 0 ? 'danger' : 'success'),
            Stat::make(__('widgets.expiring_documents.within_30_days'), (string) $within30)
                ->description(__('widgets.expiring_documents.within_30_description'))
                ->color($within30 > 0 ? 'warning' : 'success'),
            Stat::make(__('widgets.expiring_documents.within_60_days'), (string) $within60)
                ->description(__('widgets.expiring_documents.within_60_description'))
                ->color($within60 > 0 ? 'info' : 'success'),
        ];
    }

    private function base(Builder $query): Builder
    {
        return $query->where('is_active', true)->whereNotNull('expiry_date');
    }
}
