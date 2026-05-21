<?php

declare(strict_types=1);

namespace App\Models;

use Database\Factories\CorporateAccountFactory;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use OwenIt\Auditing\Auditable as AuditableTrait;
use OwenIt\Auditing\Contracts\Auditable;

class CorporateAccount extends Model implements Auditable
{
    /** @use HasFactory<CorporateAccountFactory> */
    use AuditableTrait, HasFactory, HasUuids, SoftDeletes;

    protected $fillable = [
        'company_name',
        'company_name_ar',
        'tax_id',
        'commercial_register',
        'industry',
        'address',
        'billing_email',
        'billing_phone',
        'credit_limit',
        'payment_terms_days',
        'discount_percentage',
        'is_active',
        'notes',
    ];

    protected $casts = [
        'credit_limit' => 'decimal:2',
        'payment_terms_days' => 'integer',
        'discount_percentage' => 'decimal:2',
        'is_active' => 'boolean',
    ];

    public function customers(): HasMany
    {
        return $this->hasMany(Customer::class);
    }
}
