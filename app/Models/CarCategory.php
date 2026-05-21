<?php

declare(strict_types=1);

namespace App\Models;

use App\Enums\CarCategoryClass;
use Database\Factories\CarCategoryFactory;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CarCategory extends Model
{
    /** @use HasFactory<CarCategoryFactory> */
    use HasFactory, HasUuids;

    protected $fillable = [
        'name',
        'name_ar',
        'class_code',
        'default_seats',
        'sort_order',
    ];

    protected $casts = [
        'class_code' => CarCategoryClass::class,
        'default_seats' => 'integer',
        'sort_order' => 'integer',
    ];
}
