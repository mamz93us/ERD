<?php

declare(strict_types=1);

namespace App\Models;

use App\Enums\DriverStatus;
use App\Enums\EmploymentType;
use App\Models\Concerns\BelongsToBranch;
use Database\Factories\DriverFactory;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Notifications\Notifiable;
use OwenIt\Auditing\Auditable as AuditableTrait;
use OwenIt\Auditing\Contracts\Auditable;

class Driver extends Model implements Auditable
{
    /** @use HasFactory<DriverFactory> */
    use AuditableTrait, BelongsToBranch, HasFactory, HasUuids, Notifiable, SoftDeletes;

    public function routeNotificationForWhatsapp(): ?string
    {
        return $this->whatsapp_phone ?? $this->phone;
    }

    public function preferredLocale(): string
    {
        return config('app.locale', 'ar');
    }

    protected $fillable = [
        'branch_id',
        'national_id',
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

    protected $casts = [
        'employment_type' => EmploymentType::class,
        'status' => DriverStatus::class,
        'date_of_birth' => 'date',
        'hire_date' => 'date',
        'base_salary' => 'decimal:2',
        'trip_commission_percentage' => 'decimal:2',
        'rating' => 'decimal:2',
    ];

    public function documents(): HasMany
    {
        return $this->hasMany(DriverDocument::class);
    }
}
