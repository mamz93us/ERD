<?php

declare(strict_types=1);

namespace App\Models;

use App\Enums\MaintenanceOrderStatus;
use App\Enums\MaintenanceOrderType;
use Database\Factories\MaintenanceOrderFactory;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use OwenIt\Auditing\Auditable as AuditableTrait;
use OwenIt\Auditing\Contracts\Auditable;

class MaintenanceOrder extends Model implements Auditable
{
    /** @use HasFactory<MaintenanceOrderFactory> */
    use AuditableTrait, HasFactory, HasUuids, SoftDeletes;

    protected $fillable = [
        'order_number',
        'car_id',
        'garage_id',
        'order_type',
        'description',
        'scheduled_start',
        'scheduled_end',
        'actual_start',
        'actual_end',
        'odometer_at_service',
        'subtotal',
        'vat_amount',
        'total_cost',
        'status',
        'invoice_file_path',
        'notes',
    ];

    protected $casts = [
        'order_type' => MaintenanceOrderType::class,
        'status' => MaintenanceOrderStatus::class,
        'scheduled_start' => 'datetime',
        'scheduled_end' => 'datetime',
        'actual_start' => 'datetime',
        'actual_end' => 'datetime',
        'odometer_at_service' => 'integer',
        'subtotal' => 'decimal:2',
        'vat_amount' => 'decimal:2',
        'total_cost' => 'decimal:2',
    ];

    /** @var list<string> */
    protected $auditInclude = ['status', 'total_cost'];

    public function car(): BelongsTo
    {
        return $this->belongsTo(Car::class);
    }

    public function garage(): BelongsTo
    {
        return $this->belongsTo(Garage::class);
    }

    public function items(): HasMany
    {
        return $this->hasMany(MaintenanceItem::class);
    }

    /** Auto-generate order_number = M-{year}-{NNNN} per year. */
    public static function nextNumber(): string
    {
        $year = now()->year;
        $prefix = "M-{$year}-";

        $last = static::query()
            ->where('order_number', 'like', $prefix.'%')
            ->orderByDesc('order_number')
            ->value('order_number');

        $seq = $last !== null ? ((int) substr($last, strlen($prefix))) + 1 : 1;

        return $prefix.str_pad((string) $seq, 4, '0', STR_PAD_LEFT);
    }
}
