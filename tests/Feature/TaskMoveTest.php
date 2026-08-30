<?php

use App\Models\BoardColumn;
use App\Models\Company;
use App\Models\Project;
use App\Models\Task;
use App\Models\User;

test('user can move a task to another column', function () {
    $company = Company::factory()->create();

    $user = User::factory()->create([
        'company_id' => $company->id,
        'role' => 'admin',
    ]);

    $project = Project::factory()->create([
        'company_id' => $company->id,
    ]);

    $columns = $project->boardColumns()->orderBy('position')->get();

    $todo = $columns->first();
    $progress = $columns->get(1);

    $task = Task::factory()->create([
        'project_id' => $project->id,
        'board_column_id' => $todo->id,
        'position' => 0,
    ]);

    $response = $this
        ->actingAs($user)
        ->patchJson(
            route('projects.tasks.move', [$project, $task]),
            [
                'board_column_id' => $progress->id,
                'position' => 0,
            ]
        );

    $response
        ->assertSuccessful()
        ->assertJson([
            'message' => 'Task moved successfully.',
        ]);

    $this->assertDatabaseHas('tasks', [
        'id' => $task->id,
        'project_id' => $project->id,
        'board_column_id' => $progress->id,
        'position' => 0,
    ]);
});

test('user cannot move a task to a column from another project', function () {
    $company = Company::factory()->create();

    $user = User::factory()->create([
        'company_id' => $company->id,
        'role' => 'admin',
    ]);

    $projectOne = Project::factory()->create([
        'company_id' => $company->id,
    ]);

    $projectTwo = Project::factory()->create([
        'company_id' => $company->id,
    ]);

    $task = Task::factory()->create([
        'project_id' => $projectOne->id,
        'board_column_id' => $projectOne->boardColumns()->first()->id,
        'position' => 0,
    ]);

    $otherColumn = $projectTwo->boardColumns()->first();

    $response = $this
        ->actingAs($user)
        ->patchJson(
            route('projects.tasks.move', [$projectOne, $task]),
            [
                'board_column_id' => $otherColumn->id,
                'position' => 0,
            ]
        );

    $response->assertUnprocessable();

    $this->assertDatabaseHas('tasks', [
        'id' => $task->id,
        'project_id' => $projectOne->id,
        'board_column_id' => $projectOne->boardColumns()->first()->id,
    ]);
});

test('user can reorder a task within the same column', function () {
    $company = Company::factory()->create();

    $user = User::factory()->create([
        'company_id' => $company->id,
        'role' => 'admin',
    ]);

    $project = Project::factory()->create([
        'company_id' => $company->id,
    ]);

    $column = $project->boardColumns()->first();

    $firstTask = Task::factory()->create([
        'project_id' => $project->id,
        'board_column_id' => $column->id,
        'position' => 0,
    ]);

    $secondTask = Task::factory()->create([
        'project_id' => $project->id,
        'board_column_id' => $column->id,
        'position' => 1,
    ]);

    $response = $this
        ->actingAs($user)
        ->patchJson(
            route('projects.tasks.move', [
                'project' => $project,
                'task' => $secondTask,
            ]),
            [
                'board_column_id' => $column->id,
                'position' => 0,
            ]
        );

    $response
        ->assertSuccessful()
        ->assertJson([
            'message' => 'Task moved successfully.',
        ]);

    $this->assertDatabaseHas('tasks', [
        'id' => $secondTask->id,
        'board_column_id' => $column->id,
        'position' => 0,
    ]);
});