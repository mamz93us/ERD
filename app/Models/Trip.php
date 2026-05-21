<?php

declare(strict_types=1);

namespace App\Models;

use App\Enums\TripStatus;
use App\Models\Concerns\BelongsToBranch;
use Database\Factories\TripFactory;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use OwenIt\Auditing\Auditable as AuditableTrait;
use OwenIt\Auditing\Contracts\Auditable;
use RuntimeException;

class Trip extends Model implements Auditable
{
    /** @use HasFactory<TripFactory> */
    use AuditableTrait, BelongsToBranch, HasFactory, HasUuids, SoftDeletes;

    protected $fillable = [
        'trip_number',
        'branch_id',
        'customer_id',
        'corporate_account_id',
        'car_id',
        'driver_id',
        'quotation_id',
        'rate_card_id',
        'scheduled_start',
        'scheduled_end',
        'actual_start',
        'actual_end',
        'pickup_location',
        'dropoff_location',
        'start_odometer',
        'end_odometer',
        'status',
        'cancellation_reason',
        'subtotal',
        'vat_amount',
        'total_amount',
        'notes',
    ];

    protected $casts = [
        'scheduled_start' => 'datetime',
        'scheduled_end' => 'datetime',
        'actual_start' => 'datetime',
        'actual_end' => 'datetime',
        'start_odometer' => 'integer',
        'end_odometer' => 'integer',
        'status' => TripStatus::class,
        'subtotal' => 'decimal:2',
        'vat_amount' => 'decimal:2',
        'total_amount' => 'decimal:2',
    ];

    /** @var list<string> */
    protected $auditInclude = ['status', 'cancellation_reason'];

    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }

    public function corporateAccount(): BelongsTo
    {
        return $this->belongsTo(CorporateAccount::class);
    }

    public function car(): BelongsTo
    {
        return $this->belongsTo(Car::class);
    }

    public function driver(): BelongsTo
    {
        return $this->belongsTo(Driver::class);
    }

    public function quotation(): BelongsTo
    {
        return $this->belongsTo(Quotation::class);
    }

    public function rateCard(): BelongsTo
    {
        return $this->belongsTo(RateCard::class);
    }

    public function inspections(): HasMany
    {
        return $this->hasMany(TripInspection::class);
    }

    public function expenses(): HasMany
    {
        return $this->hasMany(TripExpense::class);
    }

    public function damageReports(): HasMany
    {
        return $this->hasMany(TripDamageReport::class);
    }

    /**
     * Move the trip to a new status, validating against the transition rules
     * defined in TripStatus::allowedNext(). Throws RuntimeException on illegal
     * transition. Returns the saved model.
     *
     * Named changeStatus (not transitionTo) to avoid clashing with
     * OwenIt\Auditing\Contracts\Auditable::transitionTo(Audit) which the
     * Auditable trait provides for audit-log rollback.
     *
     * Used in: TripResource actions (confirm, assign, cancel, etc.) + Phase 8
     * InvoiceService (auto-transition from completed → invoiced).
     */
    public function changeStatus(TripStatus $next, ?string $reason = null): self
    {
        $current = $this->status;
        if (! $current->canTransitionTo($next)) {
            throw new RuntimeException(
                "Illegal trip status transition: {$current->value} → {$next->value}"
            );
        }

        $this->status = $next;
        if ($reason !== null && ($next === TripStatus::Cancelled || $next === TripStatus::NoShow)) {
            $this->cancellation_reason = $reason;
        }
        $this->save();

        return $this;
    }

    /** Auto-generate trip_number = T-{year}-{NNNN} per year. Called by TripNumberObserver. */
    public static function nextNumber(): string
    {
        $year = now()->year;
        $prefix = "T-{$year}-";

        $last = static::query()
            ->where('trip_number', 'like', $prefix.'%')
            ->orderByDesc('trip_number')
            ->value('trip_number');

        $seq = $last !== null ? ((int) substr($last, strlen($prefix))) + 1 : 1;

        return $prefix.str_pad((string) $seq, 4, '0', STR_PAD_LEFT);
    }
}
