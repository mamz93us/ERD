<?php

declare(strict_types=1);

namespace App\Models;

use App\Enums\InsuranceClaimStatus;
use Database\Factories\InsuranceClaimFactory;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use OwenIt\Auditing\Auditable as AuditableTrait;
use OwenIt\Auditing\Contracts\Auditable;

class InsuranceClaim extends Model implements Auditable
{
    /** @use HasFactory<InsuranceClaimFactory> */
    use AuditableTrait, HasFactory, HasUuids;

    protected $fillable = [
        'car_id',
        'trip_id',
        'claim_number',
        'incident_date',
        'incident_location',
        'description',
        'police_report_number',
        'claim_amount',
        'payout_amount',
        'status',
        'documents',
        'notes',
    ];

    protected $casts = [
        'incident_date' => 'datetime',
        'status' => InsuranceClaimStatus::class,
        'claim_amount' => 'decimal:2',
        'payout_amount' => 'decimal:2',
        'documents' => 'array',
    ];

    /** @var list<string> */
    protected $auditInclude = ['status', 'payout_amount'];

    public function car(): BelongsTo
    {
        return $this->belongsTo(Car::class);
    }

    public function trip(): BelongsTo
    {
        return $this->belongsTo(Trip::class);
    }
}
