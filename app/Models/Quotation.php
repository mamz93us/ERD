<?php

declare(strict_types=1);

namespace App\Models;

use App\Enums\QuotationStatus;
use Database\Factories\QuotationFactory;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use OwenIt\Auditing\Auditable as AuditableTrait;
use OwenIt\Auditing\Contracts\Auditable;

class Quotation extends Model implements Auditable
{
    /** @use HasFactory<QuotationFactory> */
    use AuditableTrait, HasFactory, HasUuids, SoftDeletes;

    protected $fillable = [
        'quotation_number',
        'customer_id',
        'corporate_account_id',
        'created_by_user_id',
        'pickup_at',
        'dropoff_at',
        'pickup_location',
        'dropoff_location',
        'estimated_distance_km',
        'category_id',
        'rate_card_id',
        'subtotal',
        'vat_amount',
        'total_amount',
        'valid_until',
        'status',
        'notes',
        'terms_and_conditions',
    ];

    protected $casts = [
        'pickup_at' => 'datetime',
        'dropoff_at' => 'datetime',
        'estimated_distance_km' => 'integer',
        'subtotal' => 'decimal:2',
        'vat_amount' => 'decimal:2',
        'total_amount' => 'decimal:2',
        'valid_until' => 'date',
        'status' => QuotationStatus::class,
    ];

    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }

    public function corporateAccount(): BelongsTo
    {
        return $this->belongsTo(CorporateAccount::class);
    }

    public function createdByUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by_user_id');
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(CarCategory::class);
    }

    public function rateCard(): BelongsTo
    {
        return $this->belongsTo(RateCard::class);
    }

    /**
     * Generate the next quotation number Q-YYYY-NNNN where NNNN is the sequence
     * within the current year. Called by QuotationNumberObserver on creating.
     */
    public static function nextNumber(): string
    {
        $year = now()->year;
        $prefix = "Q-{$year}-";

        $last = static::query()
            ->where('quotation_number', 'like', $prefix.'%')
            ->orderByDesc('quotation_number')
            ->value('quotation_number');

        $seq = $last !== null ? ((int) substr($last, strlen($prefix))) + 1 : 1;

        return $prefix.str_pad((string) $seq, 4, '0', STR_PAD_LEFT);
    }
}
