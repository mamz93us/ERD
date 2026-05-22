<?php

declare(strict_types=1);

namespace App\Models;

use App\Enums\DriverStatus;
use App\Enums\EmploymentType;
use App\Models\Concerns\BelongsToBranch;
use Database\Factories\DriverFactory;
use Illuminate\Auth\Authenticatable;
use Illuminate\Contracts\Auth\Authenticatable as AuthenticatableContract;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Notifications\Notifiable;
use OwenIt\Auditing\Auditable as AuditableTrait;
use OwenIt\Auditing\Contracts\Auditable;

class Driver extends Model implements Auditable, AuthenticatableContract
{
    /** @use HasFactory<DriverFactory> */
    use AuditableTrait, Authenticatable, BelongsToBranch, HasFactory, HasUuids, Notifiable, SoftDeletes;

    protected $fillable = [
        'branch_id',
        'national_id',
        'password',
        'last_login_at',
        'full_name',
        'full_name_ar',
        'phone',
        'whatsapp_phone',
        'address',
        'date_of_birth',
        'hire_date',
        'employment_type',
        'base_salary',
        'trip_commission_percentage',
        'status',
        'rating',
        'notes',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'password' => 'hashed',
        'last_login_at' => 'datetime',
        'employment_type' => EmploymentType::class,
        'status' => DriverStatus::class,
        'date_of_birth' => 'date',
        'hire_date' => 'date',
        'base_salary' => 'decimal:2',
        'trip_commission_percentage' => 'decimal:2',
        'rating' => 'decimal:2',
    ];

    public function routeNotificationForWhatsapp(): ?string
    {
        return $this->whatsapp_phone ?? $this->phone;
    }

    public function preferredLocale(): string
    {
        return config('app.locale', 'ar');
    }

    public function documents(): HasMany
    {
        return $this->hasMany(DriverDocument::class);
    }

    public function trips(): HasMany
    {
        return $this->hasMany(Trip::class);
    }

    public function earnings(): HasMany
    {
        return $this->hasMany(DriverEarning::class);
    }
}
