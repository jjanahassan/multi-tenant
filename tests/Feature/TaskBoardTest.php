<?php

use App\Models\Company;
use App\Models\Project;
use App\Models\Task;
use App\Models\User;

test('user can view project board with tasks', function () {
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
        ->first();

    Task::factory()->create([
        'project_id' => $project->id,
        'board_column_id' => $column->id,
        'title' => 'Build Kanban Board',
    ]);

    $response = $this
        ->actingAs($user)
        ->get(route('projects.show', $project));

    $response->assertOk();

    $response->assertSee('To Do');
    $response->assertSee('In Progress');
    $response->assertSee('Done');
    $response->assertSee('Build Kanban Board');
});