<?php

declare(strict_types=1);

namespace App\Enums;

use Filament\Support\Contracts\HasColor;
use Filament\Support\Contracts\HasLabel;

/**
 * Trip lifecycle (spec §5.5). Transition rules in canTransitionTo().
 *
 * Lifecycle: draft → confirmed → assigned → en_route → in_progress → completed → invoiced → closed
 *                                                                                       ↘ cancelled / no_show (terminal)
 */
enum TripStatus: string implements HasColor, HasLabel
{
    case Draft = 'draft';
    case Confirmed = 'confirmed';
    case Assigned = 'assigned';
    case EnRoute = 'en_route';
    case InProgress = 'in_progress';
    case Completed = 'completed';
    case Invoiced = 'invoiced';
    case Closed = 'closed';
    case Cancelled = 'cancelled';
    case NoShow = 'no_show';

    public function getLabel(): string
    {
        return __("enums.trip_status.{$this->value}");
    }

    public function getColor(): string
    {
        return match ($this) {
            self::Draft => 'gray',
            self::Confirmed, self::Assigned => 'info',
            self::EnRoute, self::InProgress => 'warning',
            self::Completed, self::Invoiced, self::Closed => 'success',
            self::Cancelled, self::NoShow => 'danger',
        };
    }

    /** @return list<self> */
    public function allowedNext(): array
    {
        return match ($this) {
            self::Draft => [self::Confirmed, self::Cancelled],
            self::Confirmed => [self::Assigned, self::Cancelled],
            self::Assigned => [self::EnRoute, self::Cancelled, self::NoShow],
            self::EnRoute => [self::InProgress, self::Cancelled],
            self::InProgress => [self::Completed],
            self::Completed => [self::Invoiced],
            self::Invoiced => [self::Closed],
            self::Closed, self::Cancelled, self::NoShow => [],
        };
    }

    public function canTransitionTo(self $next): bool
    {
        return in_array($next, $this->allowedNext(), true);
    }

    /** True for the spec-listed "active" statuses that count for overlap and trigger guards. */
    public function isActiveForOverlap(): bool
    {
        return ! in_array($this, [self::Cancelled, self::NoShow, self::Completed, self::Closed], true);
    }
}
