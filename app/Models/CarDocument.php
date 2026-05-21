<?php

declare(strict_types=1);

namespace App\Models;

use App\Enums\CarDocumentType;
use Database\Factories\CarDocumentFactory;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CarDocument extends Model
{
    /** @use HasFactory<CarDocumentFactory> */
    use HasFactory, HasUuids;

    protected $fillable = [
        'car_id',
        'doc_type',
        'document_number',
        'issue_date',
        'expiry_date',
        'issuer',
        'cost',
        'file_path',
        'is_active',
    ];

    protected $casts = [
        'doc_type' => CarDocumentType::class,
        'issue_date' => 'date',
        'expiry_date' => 'date',
        'cost' => 'decimal:2',
        'is_active' => 'boolean',
    ];

    public function car(): BelongsTo
    {
        return $this->belongsTo(Car::class);
    }

    /** @return int|null  Positive = days remaining. Negative = days already expired. Null when no expiry_date. */
    public function daysUntilExpiry(): ?int
    {
        if ($this->expiry_date === null) {
            return null;
        }

        return (int) now()->startOfDay()->diffInDays($this->expiry_date->startOfDay(), false);
    }
}
