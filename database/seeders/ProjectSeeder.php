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
        Project::insert([
            [
                'id' => (string) Str::uuid(),
                'name' => 'Project Alpha',
                'details' => json_encode(['description' => 'This is the first project']),
                'is_active' => true,
                'start_date' => now(),
            ],
            [
                'id' => (string) Str::uuid(),
                'name' => 'Project Beta',
                'details' => json_encode(['description' => 'This is the second project']),
                'is_active' => false,
                'start_date' => now()->subDays(10),
            ],
            [
                'id' => (string) Str::uuid(),
                'name' => 'Project Gamma',
                'details' => json_encode(['description' => 'This is the third project']),
                'is_active' => true,
                'start_date' => now()->addDays(5),
            ]
        ]);
    }
}
