<?php

namespace Database\Seeders;

use App\Models\Company;
use App\Models\Project;
use App\Models\Task;
use App\Models\User;
use Illuminate\Database\Seeder;

class LargeDatasetSeeder extends Seeder
{
    public function run(): void
    {
        $companies = 5;
        $usersPerCompany = 10;
        $projectsPerCompany = 5;
        $tasksPerProject = 1000;

        for ($companyIndex = 1; $companyIndex <= $companies; $companyIndex++) {
            $company = Company::factory()->create();

            $users = User::factory()
                ->count($usersPerCompany)
                ->create([
                    'company_id' => $company->id,
                    'role' => 'member',
                ]);

            $owner = $users->first();

            $owner->update([
                'role' => 'owner',
            ]);

            $company->update([
                'owner_id' => $owner->id,
            ]);

            for (
                $projectIndex = 1;
                $projectIndex <= $projectsPerCompany;
                $projectIndex++
            ) {
                $project = Project::factory()->create([
                    'company_id' => $company->id,
                ]);

                $columns = $project
                    ->boardColumns()
                    ->orderBy('position')
                    ->get();

                $tasks = [];

                for (
                    $taskIndex = 1;
                    $taskIndex <= $tasksPerProject;
                    $taskIndex++
                ) {
                    $tasks[] = [
                        'project_id' => $project->id,
                        'board_column_id' => $columns->random()->id,
                        'assignee_id' => $users->random()->id,
                        'title' => fake()->sentence(4),
                        'description' => fake()->paragraph(),
                        'due_date' => fake()->optional(0.8)->dateTimeBetween(
                            'now',
                            '+60 days'
                        ),
                        'position' => $taskIndex,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ];

                    if (count($tasks) === 500) {
                        Task::insert($tasks);
                        $tasks = [];
                    }
                }

                if ($tasks !== []) {
                    Task::insert($tasks);
                }
            }
        }
    }
}