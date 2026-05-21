<?php

declare(strict_types=1);

namespace App\Models;

use App\Enums\DriverDocumentType;
use Database\Factories\DriverDocumentFactory;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DriverDocument extends Model
{
    /** @use HasFactory<DriverDocumentFactory> */
    use HasFactory, HasUuids;

    protected $fillable = [
        'driver_id',
        'doc_type',
        'document_number',
        'issue_date',
        'expiry_date',
        'issuer',
        'file_path',
        'is_active',
    ];

    protected $casts = [
        'doc_type' => DriverDocumentType::class,
        'issue_date' => 'date',
        'expiry_date' => 'date',
        'is_active' => 'boolean',
    ];

    public function driver(): BelongsTo
    {
        return $this->belongsTo(Driver::class);
    }
}
