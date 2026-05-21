<?php

declare(strict_types=1);

namespace App\Filament\Admin\Resources\Trips;

use App\Filament\Admin\Resources\Trips\Pages\CreateTrip;
use App\Filament\Admin\Resources\Trips\Pages\EditTrip;
use App\Filament\Admin\Resources\Trips\Pages\ListTrips;
use App\Filament\Admin\Resources\Trips\RelationManagers\DamageReportsRelationManager;
use App\Filament\Admin\Resources\Trips\RelationManagers\ExpensesRelationManager;
use App\Filament\Admin\Resources\Trips\RelationManagers\InspectionsRelationManager;
use App\Filament\Admin\Resources\Trips\Schemas\TripForm;
use App\Filament\Admin\Resources\Trips\Tables\TripsTable;
use App\Models\Trip;
use App\Services\Booking\BookingAvailabilityService;
use BackedEnum;
use Carbon\CarbonImmutable;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Validation\ValidationException;

class TripResource extends Resource
{
    protected static ?string $model = Trip::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedCalendarDays;

    public static function getNavigationLabel(): string
    {
        return __('navigation.trips');
    }

    public static function getNavigationGroup(): ?string
    {
        return __('navigation.operations');
    }

    public static function getPluralModelLabel(): string
    {
        return __('navigation.trips');
    }

    public static function form(Schema $schema): Schema
    {
        return TripForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return TripsTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [
            InspectionsRelationManager::class,
            ExpensesRelationManager::class,
            DamageReportsRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListTrips::route('/'),
            'create' => CreateTrip::route('/create'),
            'edit' => EditTrip::route('/{record}/edit'),
        ];
    }

    public static function getRecordRouteBindingEloquentQuery(): Builder
    {
        return parent::getRecordRouteBindingEloquentQuery()
            ->withoutGlobalScopes([SoftDeletingScope::class]);
    }

    /**
     * Run BookingAvailabilityService and throw ValidationException with each
     * hard conflict surfaced as a flash notification + form error.
     *
     * Soft issues (document expiry inside the window) become warning notifications
     * but DO NOT block the save — per spec they're override-able.
     *
     * Called from CreateTrip::mutateFormDataBeforeCreate and
     * EditTrip::mutateFormDataBeforeSave.
     *
     * @param  array<string,mixed>  $data
     */
    public static function assertAvailable(array $data, ?string $excludeTripId = null): void
    {
        if (empty($data['car_id']) || empty($data['driver_id']) || empty($data['scheduled_start']) || empty($data['scheduled_end'])) {
            return;
        }

        $result = app(BookingAvailabilityService::class)->checkAvailability(
            carId: $data['car_id'],
            driverId: $data['driver_id'],
            scheduledStart: CarbonImmutable::parse($data['scheduled_start']),
            scheduledEnd: CarbonImmutable::parse($data['scheduled_end']),
            excludeTripId: $excludeTripId,
        );

        foreach ($result->softIssues() as $soft) {
            Notification::make()
                ->title(__('trips.booking_warning'))
                ->body($soft->message)
                ->warning()
                ->send();
        }

        if (! $result->isAvailable()) {
            $bodies = array_map(fn ($i) => '• '.$i->message, $result->hardIssues());
            Notification::make()
                ->title(__('trips.booking_blocked'))
                ->body(implode("\n", $bodies))
                ->danger()
                ->persistent()
                ->send();

            throw ValidationException::withMessages([
                'scheduled_start' => __('trips.booking_blocked'),
            ]);
        }
    }
}
