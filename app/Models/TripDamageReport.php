<?php

declare(strict_types=1);

namespace App\Models;

use App\Enums\TripDamageReportStatus;
use Database\Factories\TripDamageReportFactory;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TripDamageReport extends Model
{
    /** @use HasFactory<TripDamageReportFactory> */
    use HasFactory, HasUuids;

    protected $fillable = [
        'trip_id',
        'description',
        'damage_area',
        'photos',
        'repair_cost_estimate',
        'actual_repair_cost',
        'charged_to_customer',
        'customer_charge_amount',
        'status',
    ];

    protected $casts = [
        'damage_area' => 'array',
        'photos' => 'array',
        'repair_cost_estimate' => 'decimal:2',
        'actual_repair_cost' => 'decimal:2',
        'charged_to_customer' => 'boolean',
        'customer_charge_amount' => 'decimal:2',
        'status' => TripDamageReportStatus::class,
    ];

    public function trip(): BelongsTo
    {
        return $this->belongsTo(Trip::class);
    }
}
