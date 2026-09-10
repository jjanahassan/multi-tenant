<?php

use App\Models\BoardColumn;
use App\Models\Company;
use App\Models\Project;
use App\Models\Task;
use App\Models\User;
use Laravel\Sanctum\Sanctum;

test('authenticated user can list tasks for a project in their company', function () {
    $company = Company::factory()->create();

    $user = User::factory()->create([
        'company_id' => $company->id,
        'role' => 'owner',
    ]);

    $project = Project::factory()->create([
        'company_id' => $company->id,
    ]);

    Task::factory()->count(2)->create([
        'project_id' => $project->id,
    ]);

    Sanctum::actingAs($user);

    $response = $this->getJson(
        route('api.v1.projects.tasks.index', $project)
    );

    $response
        ->assertOk()
        ->assertJsonCount(2, 'data');
});

test('authenticated user can create a task through the api', function () {
    $company = Company::factory()->create();

    $user = User::factory()->create([
        'company_id' => $company->id,
        'role' => 'owner',
    ]);

    $project = Project::factory()->create([
        'company_id' => $company->id,
    ]);

    $column = BoardColumn::factory()->create([
        'project_id' => $project->id,
    ]);

    Sanctum::actingAs($user);

    $response = $this->postJson(
        route('api.v1.projects.tasks.store', $project),
        [
            'title' => 'API Task',
            'description' => 'Created through the API',
            'board_column_id' => $column->id,
            'position' => 1,
        ]
    );

    $response
        ->assertCreated()
        ->assertJsonPath('data.title', 'API Task')
        ->assertJsonPath('data.project_id', $project->id);

    $this->assertDatabaseHas('tasks', [
        'project_id' => $project->id,
        'title' => 'API Task',
    ]);
});

test('authenticated user can view a task through the api', function () {
    $company = Company::factory()->create();

    $user = User::factory()->create([
        'company_id' => $company->id,
        'role' => 'owner',
    ]);

    $project = Project::factory()->create([
        'company_id' => $company->id,
    ]);

    $task = Task::factory()->create([
        'project_id' => $project->id,
    ]);

    Sanctum::actingAs($user);

    $response = $this->getJson(
        route('api.v1.tasks.show', $task)
    );

    $response
        ->assertOk()
        ->assertJsonPath('data.id', $task->id)
        ->assertJsonPath('data.title', $task->title);
});

test('authenticated owner can update a task through the api', function () {
    $company = Company::factory()->create();

    $user = User::factory()->create([
        'company_id' => $company->id,
        'role' => 'owner',
    ]);

    $project = Project::factory()->create([
        'company_id' => $company->id,
    ]);

    $task = Task::factory()->create([
        'project_id' => $project->id,
        'title' => 'Old title',
    ]);

    $boardColumn = BoardColumn::factory()->create([
        'project_id' => $project->id,
    ]);

    Sanctum::actingAs($user);

    $response = $this
    ->putJson("/api/v1/tasks/{$task->id}", [
        'title' => 'Updated title',
        'description' => $task->description,
        'board_column_id' => $boardColumn->id,
        'assignee_id' => $task->assignee_id,
        'due_date' => $task->due_date?->toDateString(),
    ]);

    $response
        ->assertOk()
        ->assertJsonPath('data.title', 'Updated title');

    $this->assertDatabaseHas('tasks', [
        'id' => $task->id,
        'title' => 'Updated title',
    ]);
});

test('authenticated owner can delete a task through the api', function () {
    $company = Company::factory()->create();

    $user = User::factory()->create([
        'company_id' => $company->id,
        'role' => 'owner',
    ]);

    $project = Project::factory()->create([
        'company_id' => $company->id,
    ]);

    $task = Task::factory()->create([
        'project_id' => $project->id,
    ]);

    Sanctum::actingAs($user);

    $response = $this->deleteJson(
        route('api.v1.tasks.destroy', $task)
    );

    $response
        ->assertOk()
        ->assertJson([
            'message' => 'Task deleted successfully.',
        ]);

    $this->assertDatabaseMissing('tasks', [
        'id' => $task->id,
    ]);
});

test('api user cannot access a task belonging to another company', function () {
    $companyA = Company::factory()->create();
    $companyB = Company::factory()->create();

    $userA = User::factory()->create([
        'company_id' => $companyA->id,
        'role' => 'owner',
    ]);

    $projectB = Project::factory()->create([
        'company_id' => $companyB->id,
    ]);

    $taskB = Task::factory()->create([
        'project_id' => $projectB->id,
    ]);

    Sanctum::actingAs($userA);

    $response = $this->getJson(
        route('api.v1.tasks.show', $taskB)
    );

    $response->assertForbidden();
});

test('api user cannot list tasks from another company project', function () {
    $companyA = Company::factory()->create();
    $companyB = Company::factory()->create();

    $userA = User::factory()->create([
        'company_id' => $companyA->id,
        'role' => 'owner',
    ]);

    $projectB = Project::factory()->create([
        'company_id' => $companyB->id,
    ]);

    Task::factory()->count(2)->create([
        'project_id' => $projectB->id,
    ]);

    Sanctum::actingAs($userA);

    $response = $this->getJson(
        route('api.v1.projects.tasks.index', $projectB)
    );

    $response->assertNotFound();
});