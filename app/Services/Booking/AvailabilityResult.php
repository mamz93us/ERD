<?php

declare(strict_types=1);

namespace App\Services\Booking;

final readonly class AvailabilityResult
{
    /**
     * @param  list<AvailabilityIssue>  $issues
     */
    public function __construct(public array $issues) {}

    /** True iff there are no HARD issues. Soft issues may exist (warnings). */
    public function isAvailable(): bool
    {
        foreach ($this->issues as $issue) {
            if ($issue->severity === 'hard') {
                return false;
            }
        }

        return true;
    }

    /** Was there any issue at all (hard or soft)? Useful for UI warnings. */
    public function hasAnyIssue(): bool
    {
        return $this->issues !== [];
    }

    /** @return list<AvailabilityIssue> */
    public function hardIssues(): array
    {
        return array_values(array_filter($this->issues, fn ($i) => $i->severity === 'hard'));
    }

    /** @return list<AvailabilityIssue> */
    public function softIssues(): array
    {
        return array_values(array_filter($this->issues, fn ($i) => $i->severity === 'soft'));
    }
}
