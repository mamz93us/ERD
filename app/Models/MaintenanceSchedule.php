<?php

declare(strict_types=1);

namespace App\Models;

use App\Enums\MaintenanceServiceType;
use Database\Factories\MaintenanceScheduleFactory;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MaintenanceSchedule extends Model
{
    /** @use HasFactory<MaintenanceScheduleFactory> */
    use HasFactory, HasUuids;

    protected $fillable = [
        'car_id',
        'service_type',
        'interval_km',
        'interval_days',
        'last_done_km',
        'last_done_date',
        'next_due_km',
        'next_due_date',
        'is_active',
    ];

    protected $casts = [
        'service_type' => MaintenanceServiceType::class,
        'interval_km' => 'integer',
        'interval_days' => 'integer',
        'last_done_km' => 'integer',
        'last_done_date' => 'date',
        'next_due_km' => 'integer',
        'next_due_date' => 'date',
        'is_active' => 'boolean',
    ];

    public function car(): BelongsTo
    {
        return $this->belongsTo(Car::class);
    }
}
