<?php

declare(strict_types=1);

namespace App\Models;

use App\Enums\FuelLevel;
use App\Enums\TripInspectionStage;
use Database\Factories\TripInspectionFactory;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TripInspection extends Model
{
    /** @use HasFactory<TripInspectionFactory> */
    use HasFactory, HasUuids;

    protected $fillable = [
        'trip_id',
        'stage',
        'inspector_user_id',
        'odometer',
        'fuel_level',
        'damage_marks',
        'accessories_checklist',
        'customer_signature_path',
        'driver_signature_path',
        'photos',
        'notes',
        'performed_at',
    ];

    protected $casts = [
        'stage' => TripInspectionStage::class,
        'fuel_level' => FuelLevel::class,
        'damage_marks' => 'array',
        'accessories_checklist' => 'array',
        'photos' => 'array',
        'performed_at' => 'datetime',
        'odometer' => 'integer',
    ];

    public function trip(): BelongsTo
    {
        return $this->belongsTo(Trip::class);
    }

    public function inspector(): BelongsTo
    {
        return $this->belongsTo(User::class, 'inspector_user_id');
    }
}
