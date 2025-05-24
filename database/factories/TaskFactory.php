<?php

namespace Database\Factories;

use App\Models\Project;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Task>
 */
class TaskFactory extends Factory
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
            'project_id' => Project::factory(),
            'title' => $this->faker->sentence(4),
            'metadata' => json_encode([
                'priority' => $this->faker->randomElement(['low', 'medium', 'high']),
                'assigned_to' => $this->faker->name,
                'note' => $this->faker->text(10),
            ]),
            'is_done' => $this->faker->boolean(30),
            'due_date' => $this->faker->optional()->dateTimeBetween('now', '+1 year'),
        ];
    }
}
