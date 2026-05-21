<?php

declare(strict_types=1);

namespace App\Models;

use Database\Factories\RateCardFactory;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use OwenIt\Auditing\Auditable as AuditableTrait;
use OwenIt\Auditing\Contracts\Auditable;

class RateCard extends Model implements Auditable
{
    /** @use HasFactory<RateCardFactory> */
    use AuditableTrait, HasFactory, HasUuids;

    protected $fillable = [
        'category_id',
        'corporate_account_id',
        'name',
        'hourly_rate',
        'daily_rate',
        'weekly_rate',
        'monthly_rate',
        'included_km_per_day',
        'extra_km_rate',
        'extra_hour_rate',
        'driver_daily_allowance',
        'cross_city_surcharge',
        'effective_from',
        'effective_to',
        'is_active',
    ];

    protected $casts = [
        'hourly_rate' => 'decimal:2',
        'daily_rate' => 'decimal:2',
        'weekly_rate' => 'decimal:2',
        'monthly_rate' => 'decimal:2',
        'included_km_per_day' => 'integer',
        'extra_km_rate' => 'decimal:2',
        'extra_hour_rate' => 'decimal:2',
        'driver_daily_allowance' => 'decimal:2',
        'cross_city_surcharge' => 'decimal:2',
        'effective_from' => 'date',
        'effective_to' => 'date',
        'is_active' => 'boolean',
    ];

    public function category(): BelongsTo
    {
        return $this->belongsTo(CarCategory::class);
    }

    public function corporateAccount(): BelongsTo
    {
        return $this->belongsTo(CorporateAccount::class);
    }

    public function scopeActive(Builder $query): Builder
    {
        $today = now()->toDateString();

        return $query->where('is_active', true)
            ->where('effective_from', '<=', $today)
            ->where(fn ($q) => $q->whereNull('effective_to')->orWhere('effective_to', '>=', $today));
    }

    /**
     * Best matching active rate card for a category + (optionally) a corporate account.
     * Returns the corporate-specific card when one exists, otherwise the default (no corporate).
     * Used by PricingService::calculate().
     */
    public static function pickFor(string $categoryId, ?string $corporateAccountId): ?self
    {
        if ($corporateAccountId !== null) {
            $corporate = self::query()
                ->active()
                ->where('category_id', $categoryId)
                ->where('corporate_account_id', $corporateAccountId)
                ->orderByDesc('effective_from')
                ->first();

            if ($corporate !== null) {
                return $corporate;
            }
        }

        return self::query()
            ->active()
            ->where('category_id', $categoryId)
            ->whereNull('corporate_account_id')
            ->orderByDesc('effective_from')
            ->first();
    }
}
