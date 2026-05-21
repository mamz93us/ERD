<?php

declare(strict_types=1);

namespace App\Models;

use App\Enums\TripExpenseType;
use Database\Factories\TripExpenseFactory;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TripExpense extends Model
{
    /** @use HasFactory<TripExpenseFactory> */
    use HasFactory, HasUuids;

    protected $fillable = [
        'trip_id',
        'type',
        'amount',
        'receipt_path',
        'reimbursed',
        'notes',
        'incurred_at',
    ];

    protected $casts = [
        'type' => TripExpenseType::class,
        'amount' => 'decimal:2',
        'reimbursed' => 'boolean',
        'incurred_at' => 'datetime',
    ];

    public function trip(): BelongsTo
    {
        return $this->belongsTo(Trip::class);
    }
}
