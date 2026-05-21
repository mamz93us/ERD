<?php

declare(strict_types=1);

namespace App\Models;

use Database\Factories\PartnerAgencyFactory;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use OwenIt\Auditing\Auditable as AuditableTrait;
use OwenIt\Auditing\Contracts\Auditable;

class PartnerAgency extends Model implements Auditable
{
    /** @use HasFactory<PartnerAgencyFactory> */
    use AuditableTrait, HasFactory, HasUuids, SoftDeletes;

    protected $fillable = [
        'name',
        'name_ar',
        'contact_person',
        'phone',
        'email',
        'tax_id',
        'address',
        'credit_limit',
        'payment_terms_days',
        'is_active',
    ];

    protected $casts = [
        'credit_limit' => 'decimal:2',
        'payment_terms_days' => 'integer',
        'is_active' => 'boolean',
    ];
}
