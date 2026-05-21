<?php

declare(strict_types=1);

namespace App\Services\Booking;

/**
 * @phpstan-type IssueType 'car_conflict'|'driver_conflict'|'maintenance_conflict'|'car_document_expiry'|'driver_document_expiry'|'sub_rental_coverage'
 * @phpstan-type Severity 'hard'|'soft'
 */
final readonly class AvailabilityIssue
{
    /**
     * @param  IssueType  $type
     * @param  Severity  $severity  hard = must block booking; soft = warn user, allow override
     */
    public function __construct(
        public string $type,
        public string $severity,
        public string $message,
        public ?string $conflictingModelId = null,
        public ?string $conflictingModelClass = null,
    ) {}

    public function toArray(): array
    {
        return [
            'type' => $this->type,
            'severity' => $this->severity,
            'message' => $this->message,
            'conflicting_model_id' => $this->conflictingModelId,
            'conflicting_model_class' => $this->conflictingModelClass,
        ];
    }
}
