<?php

declare(strict_types=1);

namespace App\Filament\Admin\Widgets;

use App\Models\Driver;
use Carbon\CarbonImmutable;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;

/**
 * Spec widget: top drivers by trips completed in the last 30 days,
 * with their rating.
 */
class DriverLeaderboardWidget extends BaseWidget
{
    protected static ?int $sort = 4;

    protected int|string|array $columnSpan = 'full';

    public function getHeading(): string
    {
        return __('widgets.driver_leaderboard_title');
    }

    public function table(Table $table): Table
    {
        $monthAgo = CarbonImmutable::now()->subDays(30);

        return $table
            ->query(
                Driver::query()
                    ->select([
                        'drivers.*',
                        \DB::raw("COUNT(CASE WHEN trips.status IN ('completed','invoiced','closed') AND trips.scheduled_end >= '{$monthAgo->toDateTimeString()}' AND trips.deleted_at IS NULL THEN 1 END) AS trips_30d"),
                    ])
                    ->leftJoin('trips', 'trips.driver_id', '=', 'drivers.id')
                    ->groupBy('drivers.id')
                    ->having('trips_30d', '>', 0)
                    ->orderByDesc('trips_30d')
                    ->limit(10)
            )
            ->columns([
                TextColumn::make('full_name')
                    ->label(__('widgets.driver'))
                    ->searchable(),
                TextColumn::make('trips_30d')
                    ->label(__('widgets.trips_last_30'))
                    ->sortable(),
                TextColumn::make('rating')
                    ->label(__('widgets.rating'))
                    ->formatStateUsing(fn ($state) => number_format((float) ($state ?? 0), 2).' ★'),
                TextColumn::make('trip_commission_percentage')
                    ->label(__('widgets.commission_pct'))
                    ->suffix('%')
                    ->toggleable(),
            ])
            ->paginated(false);
    }
}
