<?php

declare(strict_types=1);

namespace App\Models;

use Database\Factories\PaymentAllocationFactory;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use OwenIt\Auditing\Auditable as AuditableTrait;
use OwenIt\Auditing\Contracts\Auditable;

class PaymentAllocation extends Model implements Auditable
{
    /** @use HasFactory<PaymentAllocationFactory> */
    use AuditableTrait, HasFactory, HasUuids;

    protected $fillable = [
        'payment_id',
        'invoice_id',
        'allocated_amount',
        'allocated_at',
    ];

    protected $casts = [
        'allocated_amount' => 'decimal:2',
        'allocated_at' => 'datetime',
    ];

    public function payment(): BelongsTo
    {
        return $this->belongsTo(Payment::class);
    }

    public function invoice(): BelongsTo
    {
        return $this->belongsTo(Invoice::class);
    }
}
