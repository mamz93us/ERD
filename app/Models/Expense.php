<?php

declare(strict_types=1);

namespace App\Models;

use App\Enums\ExpenseCategory;
use App\Enums\ExpensePaidBy;
use App\Models\Concerns\BelongsToBranch;
use Database\Factories\ExpenseFactory;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class Expense extends Model
{
    /** @use HasFactory<ExpenseFactory> */
    use BelongsToBranch, HasFactory, HasUuids, SoftDeletes;

    protected $fillable = [
        'branch_id',
        'car_id',
        'driver_id',
        'category',
        'amount',
        'expense_date',
        'paid_by',
        'paid_by_user_id',
        'description',
        'attachment_path',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'expense_date' => 'date',
        'category' => ExpenseCategory::class,
        'paid_by' => ExpensePaidBy::class,
    ];

    public function branch(): BelongsTo
    {
        return $this->belongsTo(Branch::class);
    }

    public function car(): BelongsTo
    {
        return $this->belongsTo(Car::class);
    }

    public function driver(): BelongsTo
    {
        return $this->belongsTo(Driver::class);
    }

    public function paidByUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'paid_by_user_id');
    }
}
