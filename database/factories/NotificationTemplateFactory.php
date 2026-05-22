<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\NotificationTemplate;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<NotificationTemplate>
 */
class NotificationTemplateFactory extends Factory
{
    public function definition(): array
    {
        return [
            'key' => fake()->unique()->slug(2),
            'channel' => fake()->randomElement(['whatsapp', 'mail']),
            'locale' => fake()->randomElement(['ar', 'en']),
            'subject' => fake()->sentence(),
            'body' => 'Hello {{name}}, your reference is {{ref}}.',
            'is_active' => true,
        ];
    }
}
