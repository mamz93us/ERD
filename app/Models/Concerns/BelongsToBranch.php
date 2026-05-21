<?php

declare(strict_types=1);

namespace App\Models\Concerns;

use App\Models\Branch;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Scope;

/**
 * Apply this trait to any model whose rows belong to a single branch.
 *
 * Effects:
 *  - `branch()` relationship
 *  - global scope: non-super_admin authenticated users see only their branch's rows
 *  - observer: on `creating`, fills branch_id from the auth user if missing
 *
 * Bypass the scope (e.g. for super_admin reports) via `Model::withoutGlobalScope(BranchScope::class)`.
 */
trait BelongsToBranch
{
    public static function bootBelongsToBranch(): void
    {
        static::addGlobalScope(new class implements Scope
        {
            public function apply(Builder $builder, $model): void
            {
                // Use hasUser() (no resolution side-effect) to avoid infinite
                // recursion when this scope is applied to the User model
                // itself during EloquentUserProvider::retrieveById().
                if (! app('auth')->guard()->hasUser()) {
                    return;
                }
                $user = app('auth')->guard()->user();
                if ($user === null) {
                    return;
                }
                if (method_exists($user, 'hasRole') && $user->hasRole('super_admin')) {
                    return;
                }
                if (! isset($user->branch_id) || $user->branch_id === null) {
                    return;
                }
                $builder->where($model->qualifyColumn('branch_id'), $user->branch_id);
            }
        });

        static::creating(function ($model): void {
            if (! empty($model->branch_id)) {
                return;
            }
            if (! app('auth')->guard()->hasUser()) {
                return;
            }
            $user = app('auth')->guard()->user();
            if ($user && isset($user->branch_id) && $user->branch_id !== null) {
                $model->branch_id = $user->branch_id;
            }
        });
    }

    public function branch(): BelongsTo
    {
        return $this->belongsTo(Branch::class);
    }
}
