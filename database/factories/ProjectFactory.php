<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Project>
 */
class ProjectFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'id' => (string) Str::uuid(),
            'name' => $this->faker->sentence(3),
            'details' => json_encode([
                'priority' => $this->faker->randomElement(['low', 'medium', 'high']),
                'assigned_to' => $this->faker->name,
                'note' => $this->faker->text(10),
            ]),
            'is_active' => $this->faker->boolean,
            'start_date' => $this->faker->dateTime(),
        ];
    }
}
