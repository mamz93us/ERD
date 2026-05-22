<?php

declare(strict_types=1);

namespace App\Notifications;

use App\Models\Trip;

class TripAssigned extends TemplatedNotification
{
    protected string $templateKey = 'trip_assigned';

    public function __construct(public readonly Trip $trip) {}

    protected function vars(object $notifiable): array
    {
        return [
            'driver_name' => $notifiable->full_name ?? '',
            'trip_number' => $this->trip->trip_number,
            'pickup_location' => $this->trip->pickup_location,
            'dropoff_location' => $this->trip->dropoff_location,
            'scheduled_start' => $this->trip->scheduled_start?->format('Y-m-d H:i'),
            'customer_name' => $this->trip->customer?->full_name ?? '',
        ];
    }

    protected function databasePayload(): array
    {
        return ['trip_id' => $this->trip->id, 'trip_number' => $this->trip->trip_number];
    }
}
