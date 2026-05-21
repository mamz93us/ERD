<?php

declare(strict_types=1);

namespace App\Filament\Admin\Resources\Trips\Pages;

use App\Filament\Admin\Resources\Trips\TripResource;
use App\Models\Trip;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class CreateTrip extends CreateRecord
{
    protected static string $resource = TripResource::class;

    /**
     * Wrap creation in a transaction with SELECT ... FOR UPDATE on the car and
     * driver rows so concurrent bookings can't race past the availability check
     * (CLAUDE.md §6 Phase 5). The MariaDB triggers are the last-line defense
     * against any bypass.
     *
     * @param  array<string,mixed>  $data
     * @return Trip
     */
    protected function handleRecordCreation(array $data): Model
    {
        return DB::transaction(function () use ($data) {
            DB::table('cars')->where('id', $data['car_id'])->lockForUpdate()->first();
            DB::table('drivers')->where('id', $data['driver_id'])->lockForUpdate()->first();

            TripResource::assertAvailable($data);

            return static::getResource()::getModel()::create($data);
        });
    }
}
