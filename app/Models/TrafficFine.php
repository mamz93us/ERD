<?php

declare(strict_types=1);

namespace App\Models;

use App\Enums\TrafficFinePaymentStatus;
use Database\Factories\TrafficFineFactory;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use OwenIt\Auditing\Auditable as AuditableTrait;
use OwenIt\Auditing\Contracts\Auditable;

class TrafficFine extends Model implements Auditable
{
    /** @use HasFactory<TrafficFineFactory> */
    use AuditableTrait, HasFactory, HasUuids;

    protected $fillable = [
        'car_id',
        'driver_id',
        'trip_id',
        'violation_number',
        'violation_date',
        'violation_type',
        'location',
        'amount',
        'payment_status',
        'paid_date',
        'paid_amount',
        'deducted_from_driver',
        'notes',
        'attachment_path',
    ];

    protected $casts = [
        'violation_date' => 'datetime',
        'payment_status' => TrafficFinePaymentStatus::class,
        'paid_date' => 'date',
        'amount' => 'decimal:2',
        'paid_amount' => 'decimal:2',
        'deducted_from_driver' => 'boolean',
    ];

    /** @var list<string> */
    protected $auditInclude = ['payment_status', 'deducted_from_driver', 'paid_amount'];

    public function car(): BelongsTo
    {
        return $this->belongsTo(Car::class);
    }

    public function driver(): BelongsTo
    {
        return $this->belongsTo(Driver::class);
    }

    public function trip(): BelongsTo
    {
        return $this->belongsTo(Trip::class);
    }
}
