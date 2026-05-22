<?php

declare(strict_types=1);

namespace App\Models;

use App\Enums\CustomerType;
use App\Enums\PreferredLanguage;
use Database\Factories\CustomerFactory;
use Illuminate\Auth\Authenticatable;
use Illuminate\Contracts\Auth\Authenticatable as AuthenticatableContract;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Notifications\Notifiable;
use OwenIt\Auditing\Auditable as AuditableTrait;
use OwenIt\Auditing\Contracts\Auditable;

class Customer extends Model implements Auditable, AuthenticatableContract
{
    /** @use HasFactory<CustomerFactory> */
    use AuditableTrait, Authenticatable, HasFactory, HasUuids, Notifiable, SoftDeletes;

    protected $fillable = [
        'corporate_account_id',
        'type',
        'full_name',
        'full_name_ar',
        'phone',
        'whatsapp_phone',
        'email',
        'password',
        'last_login_at',
        'national_id',
        'address',
        'preferred_language',
        'loyalty_points',
        'is_blacklisted',
        'blacklist_reason',
        'notes',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'password' => 'hashed',
        'last_login_at' => 'datetime',
        'type' => CustomerType::class,
        'preferred_language' => PreferredLanguage::class,
        'loyalty_points' => 'integer',
        'is_blacklisted' => 'boolean',
    ];

    /** @var list<string> */
    protected $auditInclude = ['is_blacklisted', 'blacklist_reason'];

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

    public function trips(): HasMany
    {
        return $this->hasMany(Trip::class);
    }

    public function quotations(): HasMany
    {
        return $this->hasMany(Quotation::class);
    }

    public function invoices(): HasMany
    {
        return $this->hasMany(Invoice::class);
    }
}
