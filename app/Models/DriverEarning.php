<?php

declare(strict_types=1);

namespace App\Models;

use Database\Factories\DriverEarningFactory;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DriverEarning extends Model
{
    /** @use HasFactory<DriverEarningFactory> */
    use HasFactory, HasUuids;

    protected $table = 'driver_earnings';

    protected $fillable = [
        'driver_id',
        'trip_id',
        'gross_commission',
        'deductions',
        'net_payable',
        'pay_period_start',
        'pay_period_end',
        'paid_at',
        'payment_reference',
    ];

    protected $casts = [
        'gross_commission' => 'decimal:2',
        'net_payable' => 'decimal:2',
        'deductions' => 'array',
        'pay_period_start' => 'date',
        'pay_period_end' => 'date',
        'paid_at' => 'datetime',
    ];

    public function driver(): BelongsTo
    {
        return $this->belongsTo(Driver::class);
    }

    public function trip(): BelongsTo
    {
        return $this->belongsTo(Trip::class);
    }
}
