<?php

declare(strict_types=1);

namespace App\Models;

use App\Enums\CarFuelType;
use App\Enums\CarOwnershipType;
use App\Enums\CarStatus;
use App\Enums\CarTransmission;
use App\Models\Concerns\BelongsToBranch;
use Database\Factories\CarFactory;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use OwenIt\Auditing\Auditable as AuditableTrait;
use OwenIt\Auditing\Contracts\Auditable;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

class Car extends Model implements Auditable, HasMedia
{
    /** @use HasFactory<CarFactory> */
    use AuditableTrait, BelongsToBranch, HasFactory, HasUuids, InteractsWithMedia, SoftDeletes;

    protected $fillable = [
        'branch_id',
        'category_id',
        'plate',
        'vin',
        'make',
        'model',
        'year',
        'color',
        'transmission',
        'fuel_type',
        'seats',
        'ownership_type',
        'status',
        'current_odometer',
        'acquisition_date',
        'acquisition_cost',
        'notes',
    ];

    protected $casts = [
        'transmission' => CarTransmission::class,
        'fuel_type' => CarFuelType::class,
        'ownership_type' => CarOwnershipType::class,
        'status' => CarStatus::class,
        'year' => 'integer',
        'seats' => 'integer',
        'current_odometer' => 'integer',
        'acquisition_date' => 'date',
        'acquisition_cost' => 'decimal:2',
    ];

    public function category(): BelongsTo
    {
        return $this->belongsTo(CarCategory::class);
    }

    public function documents(): HasMany
    {
        return $this->hasMany(CarDocument::class);
    }

    public function subRentalContracts(): HasMany
    {
        return $this->hasMany(SubRentalContract::class);
    }

    /**
     * The currently active sub-rental contract for this car (if any).
     * Used by BookingAvailabilityService in Phase 5 to gate sub-rented bookings
     * to dates inside the contract window.
     */
    public function activeSubRentalContract(): ?SubRentalContract
    {
        return $this->subRentalContracts()
            ->where('status', 'active')
            ->where('start_date', '<=', now())
            ->where('end_date', '>=', now())
            ->first();
    }

    public function scopeAvailable(Builder $query): Builder
    {
        return $query->where('status', CarStatus::Available->value);
    }

    public function scopeInMaintenance(Builder $query): Builder
    {
        return $query->where('status', CarStatus::InMaintenance->value);
    }

    /**
     * Cars with at least one active document expiring within the next $days.
     * Used by the dashboard widget and CheckCarDocumentExpiry command.
     */
    public function scopeDocumentExpiringWithin(Builder $query, int $days): Builder
    {
        $threshold = now()->addDays($days)->toDateString();

        return $query->whereHas('documents', fn (Builder $q) => $q
            ->where('is_active', true)
            ->whereNotNull('expiry_date')
            ->where('expiry_date', '<=', $threshold));
    }

    public function registerMediaCollections(): void
    {
        $this->addMediaCollection('photos')->useDisk('public');
    }

    public function registerMediaConversions(?Media $media = null): void
    {
        $this->addMediaConversion('thumb')
            ->width(320)
            ->height(240)
            ->performOnCollections('photos');
    }
}
