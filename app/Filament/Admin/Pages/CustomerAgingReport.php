<?php

declare(strict_types=1);

namespace App\Filament\Admin\Pages;

use App\Models\Branch;
use App\Models\Invoice;
use BackedEnum;
use Carbon\CarbonImmutable;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Pages\Page;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Illuminate\Support\Collection;

/**
 * Customer aging report per CLAUDE.md §6 Phase 8.
 *
 * Buckets unpaid invoice balances by how long they've been outstanding:
 *  - current (0–30 days past due)
 *  - 31–60
 *  - 61–90
 *  - 90+
 *
 * Grouped by customer (or corporate account when present), summing
 * balance_due across non-cancelled, non-paid invoices.
 */
class CustomerAgingReport extends Page
{
    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedClock;

    protected string $view = 'filament.admin.pages.customer-aging-report';

    /** @var array{as_of: string, branch_id: string|null} */
    public array $filters = [
        'as_of' => '',
        'branch_id' => null,
    ];

    public function mount(): void
    {
        $this->filters['as_of'] = CarbonImmutable::today()->toDateString();
    }

    public static function getNavigationLabel(): string
    {
        return __('navigation.customer_aging');
    }

    public static function getNavigationGroup(): ?string
    {
        return __('navigation.reports');
    }

    public function getTitle(): string
    {
        return __('navigation.customer_aging');
    }

    public function filtersForm(Schema $schema): Schema
    {
        return $schema
            ->statePath('filters')
            ->components([
                DatePicker::make('as_of')
                    ->label(__('reports.as_of'))
                    ->required()
                    ->live(),
                Select::make('branch_id')
                    ->label(__('reports.branch'))
                    ->options(fn () => Branch::query()->pluck('code', 'id'))
                    ->placeholder(__('reports.all_branches'))
                    ->nullable()
                    ->live(),
            ]);
    }

    /**
     * @return array{rows: Collection<int, array<string, mixed>>, totals: array<string, string>}
     */
    protected function getViewData(): array
    {
        $asOf = CarbonImmutable::parse($this->filters['as_of'] ?? CarbonImmutable::today()->toDateString());
        $branchId = $this->filters['branch_id'] ?? null;

        $invoices = Invoice::query()
            ->withoutGlobalScopes()
            ->with(['customer:id,full_name,full_name_ar', 'corporateAccount:id,company_name'])
            ->whereNotIn('status', ['paid', 'cancelled'])
            ->where('balance_due', '>', 0)
            ->where('issue_date', '<=', $asOf)
            ->when($branchId, function ($q) use ($branchId) {
                // Branch lives on trip; filter via the trip relationship when present.
                $q->whereHas('trip', fn ($t) => $t->where('branch_id', $branchId));
            })
            ->get();

        $rows = [];
        foreach ($invoices as $inv) {
            $daysOverdue = $asOf->diffInDays(CarbonImmutable::parse($inv->due_date), false);
            // diffInDays(false) is negative when due_date is past.
            $daysPastDue = max(0, -1 * (int) $daysOverdue);

            $bucket = match (true) {
                $daysPastDue <= 30 => 'd0_30',
                $daysPastDue <= 60 => 'd31_60',
                $daysPastDue <= 90 => 'd61_90',
                default => 'd90_plus',
            };

            $key = $inv->corporate_account_id ?? $inv->customer_id;
            $name = $inv->corporateAccount?->company_name
                ?? $inv->customer?->full_name
                ?? '—';

            if (! isset($rows[$key])) {
                $rows[$key] = [
                    'name' => $name,
                    'type' => $inv->corporate_account_id ? 'corporate' : 'individual',
                    'd0_30' => '0.00',
                    'd31_60' => '0.00',
                    'd61_90' => '0.00',
                    'd90_plus' => '0.00',
                    'total' => '0.00',
                ];
            }

            $rows[$key][$bucket] = bcadd($rows[$key][$bucket], (string) $inv->balance_due, 2);
            $rows[$key]['total'] = bcadd($rows[$key]['total'], (string) $inv->balance_due, 2);
        }

        $totals = ['d0_30' => '0.00', 'd31_60' => '0.00', 'd61_90' => '0.00', 'd90_plus' => '0.00', 'total' => '0.00'];
        foreach ($rows as $row) {
            foreach ($totals as $k => $v) {
                $totals[$k] = bcadd($v, $row[$k], 2);
            }
        }

        $rowsCollection = collect($rows)->values()->sortByDesc('total')->values();

        return [
            'rows' => $rowsCollection,
            'totals' => $totals,
            'asOf' => $asOf,
        ];
    }
}
