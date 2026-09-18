<?php

use App\Models\Company;
use App\Models\Project;
use App\Models\Task;
use App\Models\User;
use Database\Seeders\LargeDatasetSeeder;

test('large dataset seeder creates valid tenant relationships', function () {
    $this->seed(LargeDatasetSeeder::class);

    expect(Company::count())->toBe(5);
    expect(User::count())->toBe(50);
    expect(Project::count())->toBe(25);
    expect(Task::count())->toBe(25000);

    $companies = Company::with('users', 'projects')->get();

    foreach ($companies as $company) {
        expect($company->users)->toHaveCount(10);
        expect($company->projects)->toHaveCount(5);

        foreach ($company->projects as $project) {
            expect($project->company_id)->toBe($company->id);

            $taskCount = $project->tasks()->count();

            expect($taskCount)->toBe(1000);

            $task = $project->tasks()->first();

            expect($task->project_id)->toBe($project->id);
            expect($task->boardColumn->project_id)->toBe($project->id);
            expect($task->assignee->company_id)->toBe($company->id);
        }
    }
});