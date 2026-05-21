<?php

declare(strict_types=1);

namespace App\Models;

use App\Enums\MaintenanceItemType;
use Database\Factories\MaintenanceItemFactory;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MaintenanceItem extends Model
{
    /** @use HasFactory<MaintenanceItemFactory> */
    use HasFactory, HasUuids;

    protected $fillable = [
        'maintenance_order_id',
        'item_type',
        'description',
        'quantity',
        'unit_cost',
        'total_cost',
    ];

    protected $casts = [
        'item_type' => MaintenanceItemType::class,
        'quantity' => 'decimal:2',
        'unit_cost' => 'decimal:2',
        'total_cost' => 'decimal:2',
    ];

    protected static function booted(): void
    {
        static::saving(function (self $item): void {
            $item->total_cost = round((float) $item->quantity * (float) $item->unit_cost, 2);
        });
    }

    public function maintenanceOrder(): BelongsTo
    {
        return $this->belongsTo(MaintenanceOrder::class);
    }
}
