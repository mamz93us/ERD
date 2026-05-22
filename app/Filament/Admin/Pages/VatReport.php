<?php

declare(strict_types=1);

namespace App\Filament\Admin\Pages;

use App\Models\Invoice;
use App\Models\VendorBill;
use BackedEnum;
use Carbon\CarbonImmutable;
use Filament\Forms\Components\DatePicker;
use Filament\Pages\Page;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;

/**
 * VAT report for the Egyptian tax filing. Two halves:
 *  - Output VAT (sales): sum of vat_amount from invoices issued in [from, to],
 *    excluding cancelled.
 *  - Input VAT (purchases): sum of vat_amount from vendor_bills dated in
 *    [from, to], excluding draft.
 *  - Net VAT payable to the tax authority = output − input.
 *
 * Defaults to the current month. Tax period in Egypt is monthly; this page
 * is the source-of-truth view for completing the Form 10 (VAT return).
 */
class VatReport extends Page
{
    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedCalculator;

    protected string $view = 'filament.admin.pages.vat-report';

    /** @var array{from: string, to: string} */
    public array $filters = [
        'from' => '',
        'to' => '',
    ];

    public function mount(): void
    {
        $this->filters['from'] = CarbonImmutable::today()->startOfMonth()->toDateString();
        $this->filters['to'] = CarbonImmutable::today()->endOfMonth()->toDateString();
    }

    public static function getNavigationLabel(): string
    {
        return __('navigation.vat_report');
    }

    public static function getNavigationGroup(): ?string
    {
        return __('navigation.reports');
    }

    public function getTitle(): string
    {
        return __('navigation.vat_report');
    }

    public function filtersForm(Schema $schema): Schema
    {
        return $schema
            ->statePath('filters')
            ->components([
                DatePicker::make('from')
                    ->label(__('reports.from'))
                    ->required()
                    ->live(),
                DatePicker::make('to')
                    ->label(__('reports.to'))
                    ->required()
                    ->live(),
            ]);
    }

    /**
     * @return array{output: array<string,string>, input: array<string,string>, net: string, from: CarbonImmutable, to: CarbonImmutable}
     */
    protected function getViewData(): array
    {
        $from = CarbonImmutable::parse($this->filters['from'] ?? CarbonImmutable::today()->startOfMonth()->toDateString());
        $to = CarbonImmutable::parse($this->filters['to'] ?? CarbonImmutable::today()->endOfMonth()->toDateString());

        $invoiceRows = Invoice::query()
            ->withoutGlobalScopes()
            ->whereNotIn('status', ['cancelled', 'draft'])
            ->whereBetween('issue_date', [$from->toDateString(), $to->toDateString()])
            ->get(['subtotal', 'discount_amount', 'vat_amount', 'total']);

        $output = [
            'taxable' => '0.00',
            'vat' => '0.00',
            'total' => '0.00',
            'count' => $invoiceRows->count(),
        ];
        foreach ($invoiceRows as $row) {
            $taxable = bcsub((string) $row->subtotal, (string) $row->discount_amount, 2);
            $output['taxable'] = bcadd($output['taxable'], $taxable, 2);
            $output['vat'] = bcadd($output['vat'], (string) $row->vat_amount, 2);
            $output['total'] = bcadd($output['total'], (string) $row->total, 2);
        }

        $billRows = VendorBill::query()
            ->whereNotIn('status', ['draft', 'disputed'])
            ->whereBetween('bill_date', [$from->toDateString(), $to->toDateString()])
            ->get(['subtotal', 'vat_amount', 'total']);

        $input = [
            'taxable' => '0.00',
            'vat' => '0.00',
            'total' => '0.00',
            'count' => $billRows->count(),
        ];
        foreach ($billRows as $row) {
            $input['taxable'] = bcadd($input['taxable'], (string) $row->subtotal, 2);
            $input['vat'] = bcadd($input['vat'], (string) $row->vat_amount, 2);
            $input['total'] = bcadd($input['total'], (string) $row->total, 2);
        }

        $net = bcsub($output['vat'], $input['vat'], 2);

        return [
            'output' => $output,
            'input' => $input,
            'net' => $net,
            'from' => $from,
            'to' => $to,
        ];
    }
}
