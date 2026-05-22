<?php

declare(strict_types=1);

namespace App\Filament\Admin\Resources\Invoices\Pages;

use App\Filament\Admin\Resources\Invoices\InvoiceResource;
use App\Models\CorporateAccount;
use App\Models\Trip;
use App\Services\Invoicing\InvoiceService;
use Carbon\CarbonImmutable;
use Filament\Actions\Action;
use Filament\Actions\CreateAction;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ListRecords;

class ListInvoices extends ListRecords
{
    protected static string $resource = InvoiceResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Action::make('generate_from_trip')
                ->label(__('invoices.generate_from_trip'))
                ->icon('heroicon-o-bolt')
                ->color('success')
                ->schema([
                    Select::make('trip_id')
                        ->label(__('invoices.trip'))
                        ->options(fn () => Trip::query()
                            ->withoutGlobalScopes()
                            ->whereIn('status', ['completed', 'closed'])
                            ->whereDoesntHave('invoices')
                            ->orderByDesc('scheduled_end')
                            ->limit(200)
                            ->pluck('trip_number', 'id'))
                        ->searchable()
                        ->required(),
                ])
                ->action(function (array $data): void {
                    $trip = Trip::withoutGlobalScopes()->findOrFail($data['trip_id']);
                    $invoice = app(InvoiceService::class)->generateFromTrip($trip);
                    Notification::make()
                        ->title(__('invoices.generated', ['number' => $invoice->invoice_number]))
                        ->success()
                        ->send();
                }),
            Action::make('generate_consolidated')
                ->label(__('invoices.generate_consolidated'))
                ->icon('heroicon-o-document-duplicate')
                ->color('info')
                ->schema([
                    Select::make('corporate_account_id')
                        ->label(__('invoices.corporate_account'))
                        ->options(fn () => CorporateAccount::query()->where('is_active', true)->pluck('company_name', 'id'))
                        ->searchable()
                        ->required(),
                    DatePicker::make('month_start')
                        ->label(__('invoices.month_start'))
                        ->required()
                        ->default(now()->startOfMonth()),
                    DatePicker::make('month_end')
                        ->label(__('invoices.month_end'))
                        ->required()
                        ->default(now()->endOfMonth()),
                ])
                ->action(function (array $data): void {
                    $account = CorporateAccount::findOrFail($data['corporate_account_id']);
                    try {
                        $invoice = app(InvoiceService::class)->generateConsolidatedForCorporate(
                            $account,
                            CarbonImmutable::parse($data['month_start']),
                            CarbonImmutable::parse($data['month_end']),
                        );
                        Notification::make()
                            ->title(__('invoices.generated', ['number' => $invoice->invoice_number]))
                            ->success()
                            ->send();
                    } catch (\InvalidArgumentException $e) {
                        Notification::make()->title($e->getMessage())->danger()->send();
                    }
                }),
            CreateAction::make(),
        ];
    }
}
