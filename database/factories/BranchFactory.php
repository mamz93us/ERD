<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\Branch;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<Branch>
 */
class BranchFactory extends Factory
{
    public function definition(): array
    {
        return [
            'code' => Str::upper(fake()->unique()->lexify('???')),
            'name' => fake()->city(),
            'name_ar' => fake()->city(),
            'city' => fake()->city(),
            'address' => fake()->address(),
            'phone' => fake()->phoneNumber(),
            'manager_user_id' => null,
        ];
    }
}
