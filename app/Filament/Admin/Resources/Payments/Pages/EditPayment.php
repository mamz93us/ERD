<?php

declare(strict_types=1);

namespace App\Filament\Admin\Resources\Payments\Pages;

use App\Filament\Admin\Resources\Payments\PaymentResource;
use App\Models\Invoice;
use App\Models\Payment;
use App\Services\Invoicing\PaymentService;
use Filament\Actions\Action;
use Filament\Actions\DeleteAction;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\EditRecord;

class EditPayment extends EditRecord
{
    protected static string $resource = PaymentResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Action::make('allocate_to_invoices')
                ->label(__('payments.allocate'))
                ->icon('heroicon-o-paper-clip')
                ->color('success')
                ->visible(fn (Payment $record): bool => bccomp($record->unallocatedBalance(), '0.00', 2) > 0)
                ->schema([
                    Repeater::make('allocations')
                        ->label(__('payments.allocations'))
                        ->schema([
                            Select::make('invoice_id')
                                ->label(__('payments.invoice'))
                                ->options(fn ($livewire) => Invoice::query()
                                    ->where('customer_id', $livewire->record->customer_id)
                                    ->where('balance_due', '>', 0)
                                    ->pluck('invoice_number', 'id'))
                                ->searchable()
                                ->required(),
                            TextInput::make('amount')
                                ->label(__('payments.allocation_amount'))
                                ->numeric()
                                ->prefix('EGP')
                                ->required(),
                        ])
                        ->minItems(1)
                        ->columns(2),
                ])
                ->action(function (array $data, Payment $record): void {
                    $map = [];
                    foreach ($data['allocations'] as $row) {
                        $map[$row['invoice_id']] = (string) $row['amount'];
                    }
                    try {
                        app(PaymentService::class)->allocate($record, $map);
                        Notification::make()->title(__('payments.allocated'))->success()->send();
                    } catch (\Throwable $e) {
                        Notification::make()->title($e->getMessage())->danger()->send();
                    }
                }),
            DeleteAction::make(),
        ];
    }
}
