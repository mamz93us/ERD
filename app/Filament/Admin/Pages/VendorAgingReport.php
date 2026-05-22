<?php

declare(strict_types=1);

namespace App\Filament\Admin\Pages;

use App\Models\VendorBill;
use BackedEnum;
use Carbon\CarbonImmutable;
use Filament\Forms\Components\DatePicker;
use Filament\Pages\Page;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;

/**
 * Vendor aging — mirror of CustomerAgingReport, but for the payables side
 * (what we owe partner agencies, garages, fuel suppliers, insurers).
 *
 * Buckets vendor_bills.balance_due by days past due as of the chosen date.
 */
class VendorAgingReport extends Page
{
    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedClock;

    protected string $view = 'filament.admin.pages.vendor-aging-report';

    /** @var array{as_of: string} */
    public array $filters = ['as_of' => ''];

    public function mount(): void
    {
        $this->filters['as_of'] = CarbonImmutable::today()->toDateString();
    }

    public static function getNavigationLabel(): string
    {
        return __('navigation.vendor_aging');
    }

    public static function getNavigationGroup(): ?string
    {
        return __('navigation.reports');
    }

    public function getTitle(): string
    {
        return __('navigation.vendor_aging');
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
            ]);
    }

    protected function getViewData(): array
    {
        $asOf = CarbonImmutable::parse($this->filters['as_of'] ?? CarbonImmutable::today()->toDateString());

        $bills = VendorBill::query()
            ->with(['partnerAgency:id,name', 'garage:id,name'])
            ->whereNotIn('status', ['paid', 'draft'])
            ->where('balance_due', '>', 0)
            ->where('bill_date', '<=', $asOf)
            ->get();

        $rows = [];
        foreach ($bills as $bill) {
            $vendorName = $bill->partnerAgency?->name
                ?? $bill->garage?->name
                ?? ($bill->vendor_type?->getLabel() ?? '—');
            $vendorKey = $bill->partner_agency_id ?? $bill->garage_id ?? "type:{$bill->vendor_type?->value}";

            $dueDate = $bill->due_date ? CarbonImmutable::parse($bill->due_date) : CarbonImmutable::parse($bill->bill_date);
            $daysPastDue = max(0, -1 * (int) $asOf->diffInDays($dueDate, false));

            $bucket = match (true) {
                $daysPastDue <= 30 => 'd0_30',
                $daysPastDue <= 60 => 'd31_60',
                $daysPastDue <= 90 => 'd61_90',
                default => 'd90_plus',
            };

            if (! isset($rows[$vendorKey])) {
                $rows[$vendorKey] = [
                    'name' => $vendorName,
                    'type' => $bill->vendor_type?->getLabel() ?? '—',
                    'd0_30' => '0.00',
                    'd31_60' => '0.00',
                    'd61_90' => '0.00',
                    'd90_plus' => '0.00',
                    'total' => '0.00',
                ];
            }

            $rows[$vendorKey][$bucket] = bcadd($rows[$vendorKey][$bucket], (string) $bill->balance_due, 2);
            $rows[$vendorKey]['total'] = bcadd($rows[$vendorKey]['total'], (string) $bill->balance_due, 2);
        }

        $totals = ['d0_30' => '0.00', 'd31_60' => '0.00', 'd61_90' => '0.00', 'd90_plus' => '0.00', 'total' => '0.00'];
        foreach ($rows as $row) {
            foreach ($totals as $k => $v) {
                $totals[$k] = bcadd($v, $row[$k], 2);
            }
        }

        return [
            'rows' => collect($rows)->values()->sortByDesc('total')->values(),
            'totals' => $totals,
            'asOf' => $asOf,
        ];
    }
}
