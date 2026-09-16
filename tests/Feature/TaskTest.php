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

test('user can sort tasks by due date', function () {
    $company = Company::factory()->create();

    $user = User::factory()->create([
        'company_id' => $company->id,
    ]);

    $project = Project::factory()->create([
        'company_id' => $company->id,
    ]);

    $column = $project->boardColumns()->first();

    $laterTask = Task::factory()->create([
        'project_id' => $project->id,
        'board_column_id' => $column->id,
        'title' => 'Later Task',
        'due_date' => '2026-09-10',
    ]);

    $earlierTask = Task::factory()->create([
        'project_id' => $project->id,
        'board_column_id' => $column->id,
        'title' => 'Earlier Task',
        'due_date' => '2026-09-01',
    ]);

    $response = $this
        ->actingAs($user)
        ->get(route('projects.show', [
            'project' => $project,
            'sort_due_date' => 'asc',
        ]));

    $response->assertOk();

    $content = $response->getContent();

    expect(
        strpos($content, 'Earlier Task')
    )->toBeLessThan(
        strpos($content, 'Later Task')
    );
});

test('tasks can be searched by title', function () {
    $company = Company::factory()->create();

    $user = User::factory()->create([
        'company_id' => $company->id,
        'role' => 'owner',
    ]);

    $project = Project::factory()->create([
        'company_id' => $company->id,
    ]);

    $column = $project->boardColumns()
        ->where('name', 'To Do')
        ->firstOrFail();

    Task::factory()->create([
        'project_id' => $project->id,
        'board_column_id' => $column->id,
        'title' => 'Fix authentication bug',
    ]);

    Task::factory()->create([
        'project_id' => $project->id,
        'board_column_id' => $column->id,
        'title' => 'Write documentation',
    ]);

    $this->actingAs($user)
        ->get(route('projects.show', [
            'project' => $project,
            'search' => 'authentication',
        ]))
        ->assertOk()
        ->assertSee('Fix authentication bug')
        ->assertDontSee('Write documentation');
});

test('tasks can be searched by description', function () {
    $company = Company::factory()->create();

    $user = User::factory()->create([
        'company_id' => $company->id,
        'role' => 'owner',
    ]);

    $project = Project::factory()->create([
        'company_id' => $company->id,
    ]);

    $column = $project->boardColumns()
        ->where('name', 'To Do')
        ->firstOrFail();

    Task::factory()->create([
        'project_id' => $project->id,
        'board_column_id' => $column->id,
        'title' => 'Task One',
        'description' => 'Important payment integration work',
    ]);

    Task::factory()->create([
        'project_id' => $project->id,
        'board_column_id' => $column->id,
        'title' => 'Task Two',
        'description' => 'Update the homepage content',
    ]);

    $this->actingAs($user)
        ->get(route('projects.show', [
            'project' => $project,
            'search' => 'payment integration',
        ]))
        ->assertOk()
        ->assertSee('Task One')
        ->assertDontSee('Task Two');
});

test('tasks can be filtered by assignee', function () {
    $company = Company::factory()->create();

    $user = User::factory()->create([
        'company_id' => $company->id,
        'role' => 'owner',
    ]);

    $assigneeA = User::factory()->create([
        'company_id' => $company->id,
        'role' => 'member',
    ]);

    $assigneeB = User::factory()->create([
        'company_id' => $company->id,
        'role' => 'member',
    ]);

    $project = Project::factory()->create([
        'company_id' => $company->id,
    ]);

    $column = $project->boardColumns()
        ->where('name', 'To Do')
        ->firstOrFail();

    Task::factory()->create([
        'project_id' => $project->id,
        'board_column_id' => $column->id,
        'assignee_id' => $assigneeA->id,
        'title' => 'Assigned to A',
    ]);

    Task::factory()->create([
        'project_id' => $project->id,
        'board_column_id' => $column->id,
        'assignee_id' => $assigneeB->id,
        'title' => 'Assigned to B',
    ]);

    $this->actingAs($user)
        ->get(route('projects.show', [
            'project' => $project,
            'assignee_id' => $assigneeA->id,
        ]))
        ->assertOk()
        ->assertSee('Assigned to A')
        ->assertDontSee('Assigned to B');
});

test('tasks can be filtered by due date range', function () {
    $company = Company::factory()->create();

    $user = User::factory()->create([
        'company_id' => $company->id,
        'role' => 'owner',
    ]);

    $project = Project::factory()->create([
        'company_id' => $company->id,
    ]);

    $column = $project->boardColumns()
        ->where('name', 'To Do')
        ->firstOrFail();

    Task::factory()->create([
        'project_id' => $project->id,
        'board_column_id' => $column->id,
        'due_date' => '2026-09-10',
        'title' => 'Before range',
    ]);

    Task::factory()->create([
        'project_id' => $project->id,
        'board_column_id' => $column->id,
        'due_date' => '2026-09-15',
        'title' => 'Inside range',
    ]);

    Task::factory()->create([
        'project_id' => $project->id,
        'board_column_id' => $column->id,
        'due_date' => '2026-09-25',
        'title' => 'After range',
    ]);

    $this->actingAs($user)
        ->get(route('projects.show', [
            'project' => $project,
            'due_from' => '2026-09-12',
            'due_to' => '2026-09-20',
        ]))
        ->assertOk()
        ->assertSee('Inside range')
        ->assertDontSee('Before range')
        ->assertDontSee('After range');
});

test('tasks can be filtered by board column', function () {
    $company = Company::factory()->create();

    $user = User::factory()->create([
        'company_id' => $company->id,
        'role' => 'owner',
    ]);

    $project = Project::factory()->create([
        'company_id' => $company->id,
    ]);

    $todo = $project->boardColumns()
        ->where('name', 'To Do')
        ->firstOrFail();

    $done = $project->boardColumns()
        ->where('name', 'Done')
        ->firstOrFail();

    Task::factory()->create([
        'project_id' => $project->id,
        'board_column_id' => $todo->id,
        'title' => 'Todo task',
    ]);

    Task::factory()->create([
        'project_id' => $project->id,
        'board_column_id' => $done->id,
        'title' => 'Done task',
    ]);

    $this->actingAs($user)
        ->get(route('projects.show', [
            'project' => $project,
            'column_id' => $done->id,
        ]))
        ->assertOk()
        ->assertSee('Done task')
        ->assertDontSee('Todo task');
});

test('task filters can be combined', function () {
    $company = Company::factory()->create();

    $user = User::factory()->create([
        'company_id' => $company->id,
        'role' => 'owner',
    ]);

    $targetAssignee = User::factory()->create([
        'company_id' => $company->id,
        'role' => 'member',
    ]);

    $otherAssignee = User::factory()->create([
        'company_id' => $company->id,
        'role' => 'member',
    ]);

    $project = Project::factory()->create([
        'company_id' => $company->id,
    ]);

    $todo = $project->boardColumns()
        ->where('name', 'To Do')
        ->firstOrFail();

    $done = $project->boardColumns()
        ->where('name', 'Done')
        ->firstOrFail();

    Task::factory()->create([
        'project_id' => $project->id,
        'board_column_id' => $todo->id,
        'assignee_id' => $targetAssignee->id,
        'title' => 'Matching authentication task',
        'description' => 'Authentication work for the API',
        'due_date' => '2026-09-15',
    ]);

    Task::factory()->create([
        'project_id' => $project->id,
        'board_column_id' => $todo->id,
        'assignee_id' => $targetAssignee->id,
        'title' => 'Wrong date authentication task',
        'description' => 'Authentication work',
        'due_date' => '2026-10-01',
    ]);

    Task::factory()->create([
        'project_id' => $project->id,
        'board_column_id' => $done->id,
        'assignee_id' => $targetAssignee->id,
        'title' => 'Wrong column authentication task',
        'description' => 'Authentication work',
        'due_date' => '2026-09-15',
    ]);

    Task::factory()->create([
        'project_id' => $project->id,
        'board_column_id' => $todo->id,
        'assignee_id' => $otherAssignee->id,
        'title' => 'Wrong assignee authentication task',
        'description' => 'Authentication work',
        'due_date' => '2026-09-15',
    ]);

    Task::factory()->create([
        'project_id' => $project->id,
        'board_column_id' => $todo->id,
        'assignee_id' => $targetAssignee->id,
        'title' => 'Unrelated task',
        'description' => 'Something completely different',
        'due_date' => '2026-09-15',
    ]);

    $this->actingAs($user)
        ->get(route('projects.show', [
            'project' => $project,
            'search' => 'authentication',
            'assignee_id' => $targetAssignee->id,
            'due_from' => '2026-09-01',
            'due_to' => '2026-09-20',
            'column_id' => $todo->id,
        ]))
        ->assertOk()
        ->assertSee('Matching authentication task')
        ->assertDontSee('Wrong date authentication task')
        ->assertDontSee('Wrong column authentication task')
        ->assertDontSee('Wrong assignee authentication task')
        ->assertDontSee('Unrelated task');
});

test('filter validation prevents selecting a board column from another project', function () {
    $company = Company::factory()->create();

    $user = User::factory()->create([
        'company_id' => $company->id,
        'role' => 'owner',
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

    $this->actingAs($user)
        ->get(route('projects.show', [
            'project' => $projectA,
            'column_id' => $columnB->id,
        ]))
        ->assertSessionHasErrors('column_id');
});