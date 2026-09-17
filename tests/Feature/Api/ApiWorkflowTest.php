<?php

use App\Models\Company;
use App\Models\Project;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

test('complete api workflow can authenticate create task move task and delete task', function () {
    $company = Company::factory()->create();

    $user = User::factory()->create([
        'company_id' => $company->id,
        'role' => 'owner',
        'password' => Hash::make('password'),
    ]);

    /*
    |--------------------------------------------------------------------------
    | 1. Authenticate
    |--------------------------------------------------------------------------
    */

    $authResponse = $this->postJson(route('api.v1.auth.token'), [
        'email' => $user->email,
        'password' => 'password',
        'device_name' => 'Postman',
    ]);

    $authResponse
        ->assertOk()
        ->assertJsonStructure([
            'token',
            'token_type',
            'company_id',
        ]);

    $token = $authResponse->json('token');

    /*
    |--------------------------------------------------------------------------
    | 2. Create Project
    |--------------------------------------------------------------------------
    */

    $projectResponse = $this->withToken($token)
        ->postJson(route('api.v1.projects.store'), [
            'name' => 'API Acceptance Project',
            'description' => 'Created through the API workflow.',
        ]);

    $projectResponse
        ->assertCreated()
        ->assertJsonPath('data.name', 'API Acceptance Project')
        ->assertJsonPath('data.company_id', $company->id);

    $projectId = $projectResponse->json('data.id');

    /*
    |--------------------------------------------------------------------------
    | 3. Get the automatically-created board columns
    |--------------------------------------------------------------------------
    */

    $project = Project::findOrFail($projectId);

    $columns = $project
        ->boardColumns()
        ->orderBy('position')
        ->get();

    $todoColumn = $columns->firstWhere('name', 'To Do');
    $doneColumn = $columns->firstWhere('name', 'Done');

    expect($todoColumn)->not->toBeNull();
    expect($doneColumn)->not->toBeNull();

    /*
    |--------------------------------------------------------------------------
    | 4. Create Task
    |--------------------------------------------------------------------------
    */

    $taskResponse = $this->withToken($token)
        ->postJson(
            route('api.v1.projects.tasks.store', $projectId),
            [
                'title' => 'Acceptance Task',
                'description' => 'Created through the API.',
                'board_column_id' => $todoColumn->id,
                'assignee_id' => $user->id,
                'due_date' => null,
            ]
        );

    $taskResponse
        ->assertCreated()
        ->assertJsonPath('data.title', 'Acceptance Task')
        ->assertJsonPath('data.project_id', $projectId)
        ->assertJsonPath('data.board_column_id', $todoColumn->id);

    $taskId = $taskResponse->json('data.id');

    /*
    |--------------------------------------------------------------------------
    | 5. Move Task
    |--------------------------------------------------------------------------
    */

    $moveResponse = $this->withToken($token)
        ->putJson(
            route('api.v1.tasks.update', $taskId),
            [
                'title' => 'Acceptance Task',
                'description' => 'Created through the API.',
                'board_column_id' => $doneColumn->id,
                'assignee_id' => $user->id,
                'due_date' => null,
            ]
        );

    $moveResponse
        ->assertOk()
        ->assertJsonPath('data.id', $taskId)
        ->assertJsonPath('data.board_column_id', $doneColumn->id);

    /*
    |--------------------------------------------------------------------------
    | 6. Delete Task
    |--------------------------------------------------------------------------
    */

    $deleteResponse = $this->withToken($token)
        ->deleteJson(
            route('api.v1.tasks.destroy', $taskId)
        );

    $deleteResponse
        ->assertOk()
        ->assertJson([
            'message' => 'Task deleted successfully.',
        ]);

    $this->assertDatabaseMissing('tasks', [
        'id' => $taskId,
    ]);
});

test('api token cannot access another company project or task', function () {
    $companyA = Company::factory()->create();
    $companyB = Company::factory()->create();

    $userA = User::factory()->create([
        'company_id' => $companyA->id,
        'role' => 'owner',
        'password' => Hash::make('password'),
    ]);

    $userB = User::factory()->create([
        'company_id' => $companyB->id,
        'role' => 'owner',
        'password' => Hash::make('password'),
    ]);

    /*
    |--------------------------------------------------------------------------
    | Create Company B project
    |--------------------------------------------------------------------------
    |
    | Creating the project automatically creates:
    | To Do
    | In Progress
    | Done
    |
    */

    $projectB = Project::factory()->create([
        'company_id' => $companyB->id,
    ]);

    $columnB = $projectB
        ->boardColumns()
        ->where('name', 'To Do')
        ->firstOrFail();

    /*
    |--------------------------------------------------------------------------
    | Create Company B task
    |--------------------------------------------------------------------------
    */

    $taskB = $projectB->tasks()->create([
        'title' => 'Company B Task',
        'description' => 'Private Company B task.',
        'board_column_id' => $columnB->id,
        'assignee_id' => $userB->id,
        'position' => 0,
    ]);

    /*
    |--------------------------------------------------------------------------
    | Authenticate as Company A
    |--------------------------------------------------------------------------
    */

    $authResponse = $this->postJson(route('api.v1.auth.token'), [
        'email' => $userA->email,
        'password' => 'password',
        'device_name' => 'Company A Device',
    ]);

    $authResponse->assertOk();

    $tokenA = $authResponse->json('token');

    /*
    |--------------------------------------------------------------------------
    | Company A cannot access Company B project
    |--------------------------------------------------------------------------
    */

    $this->withToken($tokenA)
        ->getJson(
            route('api.v1.projects.show', $projectB)
        )
        ->assertNotFound();

    /*
    |--------------------------------------------------------------------------
    | Company A cannot access Company B task
    |--------------------------------------------------------------------------
    |
    | Tasks belong to projects rather than directly to companies.
    | Therefore TaskPolicy checks the task's project company and
    | returns 403 when the authenticated user belongs to another company.
    |
    */

    $this->withToken($tokenA)
        ->getJson(
            route('api.v1.tasks.show', $taskB)
        )
        ->assertForbidden();

    /*
    |--------------------------------------------------------------------------
    | Company A cannot list tasks belonging to Company B's project
    |--------------------------------------------------------------------------
    */

    $this->withToken($tokenA)
        ->getJson(
            route('api.v1.projects.tasks.index', $projectB)
        )
        ->assertNotFound();
});