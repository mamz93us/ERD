<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Enums\CreditNoteReason;
use App\Enums\CreditNoteStatus;
use App\Models\CreditNote;
use App\Models\Invoice;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<CreditNote>
 */
class CreditNoteFactory extends Factory
{
    public function definition(): array
    {
        return [
            'invoice_id' => Invoice::factory(),
            'created_by_user_id' => User::factory(),
            'approved_by_user_id' => null,
            'issue_date' => fake()->date(),
            'reason' => fake()->randomElement(CreditNoteReason::cases()),
            'reason_details' => fake()->sentence(),
            'amount' => (string) fake()->randomFloat(2, 100, 1000),
            'status' => CreditNoteStatus::Draft,
        ];
    }
}
