<?php

namespace Database\Seeders;

use App\Models\Attachment;
use App\Models\Task;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class AttachmentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $tasks = Task::all();
        foreach ($tasks as $task) {
            Attachment::factory()->count(3)->create([
                'task_id' => $task->id,
            ]);
        }
    }
}
