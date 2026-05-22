<?php

declare(strict_types=1);

namespace App\Models;

use App\Enums\PaymentMethod;
use App\Models\Concerns\BelongsToBranch;
use Database\Factories\PaymentFactory;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use OwenIt\Auditing\Auditable as AuditableTrait;
use OwenIt\Auditing\Contracts\Auditable;

class Payment extends Model implements Auditable
{
    /** @use HasFactory<PaymentFactory> */
    use AuditableTrait, BelongsToBranch, HasFactory, HasUuids, SoftDeletes;

    protected $fillable = [
        'payment_number',
        'customer_id',
        'corporate_account_id',
        'method',
        'amount',
        'payment_date',
        'reference_number',
        'received_by_user_id',
        'branch_id',
        'notes',
        'is_reconciled',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'payment_date' => 'date',
        'method' => PaymentMethod::class,
        'is_reconciled' => 'boolean',
    ];

    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }

    public function corporateAccount(): BelongsTo
    {
        return $this->belongsTo(CorporateAccount::class);
    }

    public function receivedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'received_by_user_id');
    }

    public function branch(): BelongsTo
    {
        return $this->belongsTo(Branch::class);
    }

    public function allocations(): HasMany
    {
        return $this->hasMany(PaymentAllocation::class);
    }

    public function allocatedTotal(): string
    {
        return (string) $this->allocations()->sum('allocated_amount');
    }

    public function unallocatedBalance(): string
    {
        return bcsub((string) $this->amount, $this->allocatedTotal(), 2);
    }

    public static function nextNumber(): string
    {
        $year = now()->year;
        $prefix = "P-{$year}-";

        $last = static::withTrashed()
            ->where('payment_number', 'like', $prefix.'%')
            ->orderByDesc('payment_number')
            ->value('payment_number');

        $seq = $last !== null ? ((int) substr($last, strlen($prefix))) + 1 : 1;

        return $prefix.str_pad((string) $seq, 4, '0', STR_PAD_LEFT);
    }
}
