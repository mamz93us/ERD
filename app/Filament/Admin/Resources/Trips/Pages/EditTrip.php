<?php

declare(strict_types=1);

namespace App\Filament\Admin\Resources\Trips\Pages;

use App\Enums\TripStatus;
use App\Filament\Admin\Resources\Trips\TripResource;
use App\Models\Trip;
use Filament\Actions\Action;
use Filament\Actions\DeleteAction;
use Filament\Actions\ForceDeleteAction;
use Filament\Actions\RestoreAction;
use Filament\Forms\Components\Textarea;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Support\Facades\DB;
use Throwable;

class EditTrip extends EditRecord
{
    protected static string $resource = TripResource::class;

    /**
     * @param  array<string,mixed>  $data
     * @return array<string,mixed>
     */
    protected function mutateFormDataBeforeSave(array $data): array
    {
        $data = parent::mutateFormDataBeforeSave($data);
        // Re-check availability on edits in case dates/car/driver changed.
        TripResource::assertAvailable($data, excludeTripId: $this->getRecord()->getKey());

        return $data;
    }

    protected function getHeaderActions(): array
    {
        return [
            ...$this->statusTransitionActions(),
            DeleteAction::make(),
            ForceDeleteAction::make(),
            RestoreAction::make(),
        ];
    }

    /** @return list<Action> */
    private function statusTransitionActions(): array
    {
        /** @var Trip $trip */
        $trip = $this->getRecord();
        $allowed = $trip->status->allowedNext();

        return array_map(function (TripStatus $next) use ($trip): Action {
            $needsReason = in_array($next, [TripStatus::Cancelled, TripStatus::NoShow], true);

            return Action::make('change_status_'.$next->value)
                ->label(__('trips.move_to', ['status' => $next->getLabel()]))
                ->icon('heroicon-o-arrow-right-circle')
                ->color($next->getColor())
                ->requiresConfirmation()
                ->schema($needsReason
                    ? [Textarea::make('reason')->label(__('trips.cancellation_reason'))->required()->rows(3)]
                    : [])
                ->action(function (array $data) use ($trip, $next): void {
                    try {
                        DB::transaction(function () use ($trip, $next, $data): void {
                            $trip->changeStatus($next, $data['reason'] ?? null);
                        });
                        Notification::make()->title(__('trips.status_changed'))->success()->send();
                        $this->refreshFormData(['status', 'cancellation_reason']);
                    } catch (Throwable $e) {
                        Notification::make()->title(__('trips.status_change_failed'))->body($e->getMessage())->danger()->send();
                    }
                });
        }, $allowed);
    }
}
