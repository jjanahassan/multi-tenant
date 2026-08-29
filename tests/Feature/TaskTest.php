<?php

use App\Models\BoardColumn;
use App\Models\Company;
use App\Models\Project;
use App\Models\Task;
use App\Models\User;

test('user can create a task', function () {
    $company = Company::factory()->create();

    $user = User::factory()->create([
        'company_id' => $company->id,
        'role' => 'admin',
    ]);

    $project = Project::factory()->create([
        'company_id' => $company->id,
    ]);

    $column = $project->boardColumns()
        ->where('name', 'To Do')
        ->firstOrFail();

    $response = $this
        ->actingAs($user)
        ->post(
            route('projects.tasks.store', $project),
            [
                'title' => 'Build login page',
                'description' => 'Create the login interface.',
                'board_column_id' => $column->id,
                'due_date' => '2026-09-10',
            ]
        );

    $response->assertRedirect();

    $this->assertDatabaseHas('tasks', [
        'project_id' => $project->id,
        'board_column_id' => $column->id,
        'title' => 'Build login page',
    ]);
});

test('user can update a task', function () {
    $company = Company::factory()->create();

    $user = User::factory()->create([
        'company_id' => $company->id,
        'role' => 'admin',
    ]);

    $project = Project::factory()->create([
        'company_id' => $company->id,
    ]);

    $column = $project->boardColumns()
        ->where('name', 'To Do')
        ->firstOrFail();

    $task = Task::factory()->create([
        'project_id' => $project->id,
        'board_column_id' => $column->id,
        'title' => 'Old title',
    ]);

    $response = $this
        ->actingAs($user)
        ->put(
            route('projects.tasks.update', [
                $project,
                $task,
            ]),
            [
                'title' => 'Updated title',
                'description' => 'Updated description',
                'board_column_id' => $column->id,
                'due_date' => '2026-09-15',
            ]
        );

    $response->assertRedirect();

    $this->assertDatabaseHas('tasks', [
        'id' => $task->id,
        'title' => 'Updated title',
    ]);
});

test('user can delete a task', function () {
    $company = Company::factory()->create();

    $user = User::factory()->create([
        'company_id' => $company->id,
        'role' => 'admin',
    ]);

    $project = Project::factory()->create([
        'company_id' => $company->id,
    ]);

    $column = $project->boardColumns()
        ->where('name', 'To Do')
        ->firstOrFail();

    $task = Task::factory()->create([
        'project_id' => $project->id,
        'board_column_id' => $column->id,
    ]);

    $response = $this
        ->actingAs($user)
        ->delete(
            route('projects.tasks.destroy', [
                $project,
                $task,
            ])
        );

    $response->assertRedirect();

    $this->assertDatabaseMissing('tasks', [
        'id' => $task->id,
    ]);
});

test('task requires a title', function () {
    $company = Company::factory()->create();

    $user = User::factory()->create([
        'company_id' => $company->id,
        'role' => 'admin',
    ]);

    $project = Project::factory()->create([
        'company_id' => $company->id,
    ]);

    $column = $project->boardColumns()
        ->where('name', 'To Do')
        ->firstOrFail();

    $response = $this
        ->actingAs($user)
        ->post(
            route('projects.tasks.store', $project),
            [
                'title' => '',
                'board_column_id' => $column->id,
            ]
        );

    $response
        ->assertRedirect()
        ->assertSessionHasErrors('title');

    $this->assertDatabaseCount('tasks', 0);
});

test('task cannot use a column from another project', function () {
    $company = Company::factory()->create();

    $user = User::factory()->create([
        'company_id' => $company->id,
        'role' => 'admin',
    ]);

    $projectA = Project::factory()->create([
        'company_id' => $company->id,
    ]);

    $projectB = Project::factory()->create([
        'company_id' => $company->id,
    ]);

    $columnB = $projectB->boardColumns()
        ->where('name', 'To Do')
        ->firstOrFail();

    $response = $this
        ->actingAs($user)
        ->post(
            route('projects.tasks.store', $projectA),
            [
                'title' => 'Invalid task',
                'board_column_id' => $columnB->id,
            ]
        );

    $response
        ->assertRedirect()
        ->assertSessionHasErrors('board_column_id');

    $this->assertDatabaseCount('tasks', 0);
});

test('task cannot be assigned to a user from another company', function () {
    $companyA = Company::factory()->create();
    $companyB = Company::factory()->create();

    $userA = User::factory()->create([
        'company_id' => $companyA->id,
        'role' => 'admin',
    ]);

    $userB = User::factory()->create([
        'company_id' => $companyB->id,
        'role' => 'member',
    ]);

    $project = Project::factory()->create([
        'company_id' => $companyA->id,
    ]);

    $column = $project->boardColumns()
        ->where('name', 'To Do')
        ->firstOrFail();

    $response = $this
        ->actingAs($userA)
        ->post(
            route('projects.tasks.store', $project),
            [
                'title' => 'Invalid assignment',
                'board_column_id' => $column->id,
                'assignee_id' => $userB->id,
            ]
        );

    $response
        ->assertRedirect()
        ->assertSessionHasErrors('assignee_id');

    $this->assertDatabaseCount('tasks', 0);
});

test('user cannot update a task through another project', function () {
    $company = Company::factory()->create();

    $user = User::factory()->create([
        'company_id' => $company->id,
        'role' => 'admin',
    ]);

    $projectA = Project::factory()->create([
        'company_id' => $company->id,
    ]);

    $projectB = Project::factory()->create([
        'company_id' => $company->id,
    ]);

    $columnB = $projectB->boardColumns()
        ->where('name', 'To Do')
        ->firstOrFail();

    $task = Task::factory()->create([
        'project_id' => $projectB->id,
        'board_column_id' => $columnB->id,
    ]);

    $columnA = $projectA->boardColumns()
        ->where('name', 'To Do')
        ->firstOrFail();

    $response = $this
        ->actingAs($user)
        ->put(
            route('projects.tasks.update', [
                'project' => $projectA,
                'task' => $task,
            ]),
            [
                'title' => 'Should not update',
                'board_column_id' => $columnA->id,
            ]
        );

    $response->assertNotFound();
});