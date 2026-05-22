<?php

declare(strict_types=1);

namespace App\Models;

use App\Enums\CreditNoteReason;
use App\Enums\CreditNoteStatus;
use Database\Factories\CreditNoteFactory;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use OwenIt\Auditing\Auditable as AuditableTrait;
use OwenIt\Auditing\Contracts\Auditable;

class CreditNote extends Model implements Auditable
{
    /** @use HasFactory<CreditNoteFactory> */
    use AuditableTrait, HasFactory, HasUuids, SoftDeletes;

    protected $fillable = [
        'note_number',
        'invoice_id',
        'created_by_user_id',
        'approved_by_user_id',
        'issue_date',
        'reason',
        'reason_details',
        'amount',
        'status',
        'approved_at',
        'applied_at',
        'pdf_path',
        'e_invoice_reference',
    ];

    protected $casts = [
        'issue_date' => 'date',
        'amount' => 'decimal:2',
        'reason' => CreditNoteReason::class,
        'status' => CreditNoteStatus::class,
        'approved_at' => 'datetime',
        'applied_at' => 'datetime',
    ];

    public function invoice(): BelongsTo
    {
        return $this->belongsTo(Invoice::class);
    }

    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by_user_id');
    }

    public function approvedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'approved_by_user_id');
    }

    public static function nextNumber(): string
    {
        $year = now()->year;
        $prefix = "CN-{$year}-";

        $last = static::withTrashed()
            ->where('note_number', 'like', $prefix.'%')
            ->orderByDesc('note_number')
            ->value('note_number');

        $seq = $last !== null ? ((int) substr($last, strlen($prefix))) + 1 : 1;

        return $prefix.str_pad((string) $seq, 4, '0', STR_PAD_LEFT);
    }
}
