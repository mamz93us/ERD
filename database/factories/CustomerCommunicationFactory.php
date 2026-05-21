<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Enums\CommunicationChannel;
use App\Enums\CommunicationDirection;
use App\Models\Customer;
use App\Models\CustomerCommunication;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<CustomerCommunication>
 */
class CustomerCommunicationFactory extends Factory
{
    public function definition(): array
    {
        return [
            'customer_id' => Customer::factory(),
            'user_id' => null,
            'channel' => fake()->randomElement(CommunicationChannel::cases()),
            'direction' => fake()->randomElement(CommunicationDirection::cases()),
            'subject' => fake()->optional()->sentence(),
            'body' => fake()->paragraph(),
            'attachments' => null,
            'external_message_id' => null,
            'sent_at' => fake()->dateTimeBetween('-3 months', 'now'),
        ];
    }
}
