<?php

declare(strict_types=1);

namespace App\Models;

use Database\Factories\GarageFactory;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Garage extends Model
{
    /** @use HasFactory<GarageFactory> */
    use HasFactory, HasUuids;

    protected $fillable = [
        'name',
        'phone',
        'address',
        'is_internal',
        'specialties',
        'is_active',
    ];

    protected $casts = [
        'is_internal' => 'boolean',
        'specialties' => 'array',
        'is_active' => 'boolean',
    ];
}
