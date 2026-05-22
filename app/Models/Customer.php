<?php

declare(strict_types=1);

namespace App\Models;

use App\Enums\CustomerType;
use App\Enums\PreferredLanguage;
use Database\Factories\CustomerFactory;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Notifications\Notifiable;
use OwenIt\Auditing\Auditable as AuditableTrait;
use OwenIt\Auditing\Contracts\Auditable;

class Customer extends Model implements Auditable
{
    /** @use HasFactory<CustomerFactory> */
    use AuditableTrait, HasFactory, HasUuids, Notifiable, SoftDeletes;

    public function routeNotificationForWhatsapp(): ?string
    {
        return $this->whatsapp_phone ?? $this->phone;
    }

    public function routeNotificationForMail(): ?string
    {
        return $this->email;
    }

    public function preferredLocale(): string
    {
        return $this->preferred_language?->value ?? config('app.locale', 'ar');
    }

    protected $fillable = [
        'corporate_account_id',
        'type',
        'full_name',
        'full_name_ar',
        'phone',
        'whatsapp_phone',
        'email',
        'national_id',
        'address',
        'preferred_language',
        'loyalty_points',
        'is_blacklisted',
        'blacklist_reason',
        'notes',
    ];

    protected $casts = [
        'type' => CustomerType::class,
        'preferred_language' => PreferredLanguage::class,
        'loyalty_points' => 'integer',
        'is_blacklisted' => 'boolean',
    ];

    /** @var list<string> */
    protected $auditInclude = ['is_blacklisted', 'blacklist_reason'];

    public function corporateAccount(): BelongsTo
    {
        return $this->belongsTo(CorporateAccount::class);
    }

    public function communications(): HasMany
    {
        return $this->hasMany(CustomerCommunication::class);
    }

    public function leads(): HasMany
    {
        return $this->hasMany(Lead::class);
    }
}
