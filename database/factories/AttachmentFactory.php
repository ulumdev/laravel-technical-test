<?php

namespace Database\Factories;

use App\Models\Task;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Attachment>
 */
class AttachmentFactory extends Factory
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
            'task_id' => Task::factory(),
            // 'filename' => $this->faker->word . '.pdf',
            'file_path' => $this->faker->word() . '/attachments/' . $this->faker->uuid() . '.pdf',
            'is_public' => $this->faker->boolean(20),
            'uploaded_at' => $this->faker->dateTime(),
        ];
    }
}
