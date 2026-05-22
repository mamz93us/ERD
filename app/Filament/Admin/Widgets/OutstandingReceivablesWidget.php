<?php

declare(strict_types=1);

namespace App\Filament\Admin\Widgets;

use App\Models\Customer;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;

/**
 * Spec widget: top 10 customers by outstanding balance (sum of
 * invoices.balance_due where status not in paid/cancelled).
 */
class OutstandingReceivablesWidget extends BaseWidget
{
    protected static ?int $sort = 3;

    protected int|string|array $columnSpan = 'full';

    public function getHeading(): string
    {
        return __('widgets.receivables_title');
    }

    public function table(Table $table): Table
    {
        return $table
            ->query(
                Customer::query()
                    ->withoutGlobalScopes()
                    ->select([
                        'customers.*',
                        \DB::raw("COALESCE(SUM(CASE WHEN invoices.status NOT IN ('paid','cancelled') AND invoices.deleted_at IS NULL THEN invoices.balance_due ELSE 0 END), 0) AS outstanding"),
                    ])
                    ->leftJoin('invoices', 'invoices.customer_id', '=', 'customers.id')
                    ->groupBy('customers.id')
                    ->having('outstanding', '>', 0)
                    ->orderByDesc('outstanding')
                    ->limit(10)
            )
            ->columns([
                TextColumn::make('full_name')
                    ->label(__('widgets.customer'))
                    ->searchable(),
                TextColumn::make('phone')
                    ->label(__('widgets.phone'))
                    ->toggleable(),
                TextColumn::make('outstanding')
                    ->label(__('widgets.outstanding_balance'))
                    ->money('EGP')
                    ->sortable(),
            ])
            ->paginated(false);
    }
}
