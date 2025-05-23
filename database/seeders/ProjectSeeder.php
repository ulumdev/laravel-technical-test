<?php

namespace Database\Seeders;

use App\Models\Project;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class ProjectSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Project::create([
            'id' => Str::uuid(),
            'name' => 'Project Alpha',
            'details' => json_encode(['description' => 'This is the first project']),
            'is_active' => true,
            'start_date' => now(),
        ]);
    }
}
