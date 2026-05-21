<?php

declare(strict_types=1);

namespace App\Filament\Admin\Resources\TrafficFines\Pages;

use App\Filament\Admin\Resources\TrafficFines\TrafficFineResource;
use App\Models\TrafficFine;
use Filament\Actions\Action;
use Filament\Actions\DeleteAction;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\EditRecord;

class EditTrafficFine extends EditRecord
{
    protected static string $resource = TrafficFineResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Action::make('deduct_from_driver_payroll')
                ->label(__('traffic_fines.deduct_action'))
                ->icon('heroicon-o-banknotes')
                ->color('warning')
                ->requiresConfirmation()
                ->modalDescription(__('traffic_fines.deduct_action_confirm'))
                ->visible(fn (TrafficFine $record): bool => ! $record->deducted_from_driver && $record->driver_id !== null)
                ->action(function (TrafficFine $record): void {
                    $record->update(['deducted_from_driver' => true]);
                    Notification::make()
                        ->title(__('traffic_fines.deduct_action_done'))
                        ->body(__('traffic_fines.deduct_action_done_body', [
                            'amount' => number_format((float) $record->amount, 2),
                            'driver' => $record->driver?->full_name ?? '—',
                        ]))
                        ->success()
                        ->send();
                    $this->refreshFormData(['deducted_from_driver']);
                }),
            Action::make('undo_deduction')
                ->label(__('traffic_fines.undo_deduct_action'))
                ->icon('heroicon-o-arrow-uturn-left')
                ->color('gray')
                ->requiresConfirmation()
                ->visible(fn (TrafficFine $record): bool => $record->deducted_from_driver)
                ->action(function (TrafficFine $record): void {
                    $record->update(['deducted_from_driver' => false]);
                    Notification::make()
                        ->title(__('traffic_fines.undo_deduct_done'))
                        ->success()
                        ->send();
                    $this->refreshFormData(['deducted_from_driver']);
                }),
            DeleteAction::make(),
        ];
    }
}
