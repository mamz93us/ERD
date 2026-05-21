<?php

declare(strict_types=1);

namespace App\Models;

use App\Enums\SubRentalContractStatus;
use Database\Factories\SubRentalContractFactory;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon;

class SubRentalContract extends Model
{
    /** @use HasFactory<SubRentalContractFactory> */
    use HasFactory, HasUuids;

    protected $fillable = [
        'partner_agency_id',
        'car_id',
        'start_date',
        'end_date',
        'daily_cost',
        'included_km_per_day',
        'extra_km_cost',
        'terms',
        'status',
        'contract_file_path',
    ];

    protected $casts = [
        'status' => SubRentalContractStatus::class,
        'start_date' => 'date',
        'end_date' => 'date',
        'daily_cost' => 'decimal:2',
        'extra_km_cost' => 'decimal:2',
        'included_km_per_day' => 'integer',
    ];

    public function partnerAgency(): BelongsTo
    {
        return $this->belongsTo(PartnerAgency::class);
    }

    public function car(): BelongsTo
    {
        return $this->belongsTo(Car::class);
    }

    public function scopeActive(Builder $query): Builder
    {
        return $query->where('status', SubRentalContractStatus::Active->value);
    }

    /**
     * Whether this contract fully covers the given date range.
     * Used by BookingAvailabilityService in Phase 5 to verify that a sub_rented
     * car's contract covers the entire requested trip window.
     */
    public function coversDateRange(Carbon $start, Carbon $end): bool
    {
        return $this->status === SubRentalContractStatus::Active
            && $this->start_date->lte($start->copy()->startOfDay())
            && $this->end_date->gte($end->copy()->endOfDay());
    }
}
