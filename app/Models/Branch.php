<?php

declare(strict_types=1);

namespace App\Models;

use Database\Factories\BranchFactory;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use OwenIt\Auditing\Auditable as AuditableTrait;
use OwenIt\Auditing\Contracts\Auditable;

class Branch extends Model implements Auditable
{
    /** @use HasFactory<BranchFactory> */
    use AuditableTrait, HasFactory, HasUuids, SoftDeletes;

    protected $fillable = [
        'code',
        'name',
        'name_ar',
        'city',
        'address',
        'phone',
        'manager_user_id',
    ];

    public function manager(): BelongsTo
    {
        return $this->belongsTo(User::class, 'manager_user_id');
    }

    public function users(): HasMany
    {
        return $this->hasMany(User::class);
    }

    public function getDisplayNameAttribute(): string
    {
        return app()->getLocale() === 'ar' ? $this->name_ar : $this->name;
    }
}
