<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\GuestUser>
 */
class GuestUserFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => $this->faker->name(),
            'date_of_birth' => $this->faker->dateTimeBetween('-100 years', 'now'),
            'created_at' => now(),
            'updated_at' => now(),
        ];
    }
}
