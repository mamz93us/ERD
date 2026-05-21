<?php

declare(strict_types=1);

namespace App\Models;

use App\Enums\LeadSource;
use App\Enums\LeadStatus;
use Database\Factories\LeadFactory;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Lead extends Model
{
    /** @use HasFactory<LeadFactory> */
    use HasFactory, HasUuids;

    protected $fillable = [
        'customer_id',
        'assigned_user_id',
        'source',
        'status',
        'requirements',
        'estimated_value',
        'lost_reason',
        'due_at',
        'closed_at',
    ];

    protected $casts = [
        'source' => LeadSource::class,
        'status' => LeadStatus::class,
        'estimated_value' => 'decimal:2',
        'due_at' => 'datetime',
        'closed_at' => 'datetime',
    ];

    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }

    public function assignedUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'assigned_user_id');
    }
}
