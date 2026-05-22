<?php

declare(strict_types=1);

namespace App\Models;

use App\Enums\VendorBillStatus;
use App\Enums\VendorType;
use Database\Factories\VendorBillFactory;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use OwenIt\Auditing\Auditable as AuditableTrait;
use OwenIt\Auditing\Contracts\Auditable;

class VendorBill extends Model implements Auditable
{
    /** @use HasFactory<VendorBillFactory> */
    use AuditableTrait, HasFactory, HasUuids, SoftDeletes;

    protected $fillable = [
        'bill_number',
        'vendor_type',
        'partner_agency_id',
        'garage_id',
        'bill_date',
        'due_date',
        'subtotal',
        'vat_amount',
        'total',
        'paid_amount',
        'balance_due',
        'status',
        'related_car_id',
        'related_sub_rental_contract_id',
        'description',
        'attachment_path',
    ];

    protected $casts = [
        'bill_date' => 'date',
        'due_date' => 'date',
        'subtotal' => 'decimal:2',
        'vat_amount' => 'decimal:2',
        'total' => 'decimal:2',
        'paid_amount' => 'decimal:2',
        'balance_due' => 'decimal:2',
        'vendor_type' => VendorType::class,
        'status' => VendorBillStatus::class,
    ];

    public function partnerAgency(): BelongsTo
    {
        return $this->belongsTo(PartnerAgency::class);
    }

    public function garage(): BelongsTo
    {
        return $this->belongsTo(Garage::class);
    }

    public function relatedCar(): BelongsTo
    {
        return $this->belongsTo(Car::class, 'related_car_id');
    }

    public function relatedSubRentalContract(): BelongsTo
    {
        return $this->belongsTo(SubRentalContract::class, 'related_sub_rental_contract_id');
    }

    public static function nextNumber(): string
    {
        $year = now()->year;
        $prefix = "VB-{$year}-";

        $last = static::withTrashed()
            ->where('bill_number', 'like', $prefix.'%')
            ->orderByDesc('bill_number')
            ->value('bill_number');

        $seq = $last !== null ? ((int) substr($last, strlen($prefix))) + 1 : 1;

        return $prefix.str_pad((string) $seq, 4, '0', STR_PAD_LEFT);
    }
}
