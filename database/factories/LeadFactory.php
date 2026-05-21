<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Enums\LeadSource;
use App\Enums\LeadStatus;
use App\Models\Lead;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Lead>
 */
class LeadFactory extends Factory
{
    public function definition(): array
    {
        return [
            'customer_id' => null,
            'assigned_user_id' => null,
            'source' => fake()->randomElement(LeadSource::cases()),
            'status' => LeadStatus::New_,
            'requirements' => fake()->paragraph(),
            'estimated_value' => fake()->randomFloat(2, 1000, 100000),
            'lost_reason' => null,
            'due_at' => fake()->optional()->dateTimeBetween('now', '+30 days'),
            'closed_at' => null,
        ];
    }
}
