<?php

use App\Models\BoardColumn;
use App\Models\Company;
use App\Models\Project;
use App\Models\User;

test('user can add a board column', function () {
    $company = Company::factory()->create();

    $user = User::factory()->create([
        'company_id' => $company->id,
    ]);

    $project = Project::factory()->create([
        'company_id' => $company->id,
    ]);

    $response = $this
        ->actingAs($user)
        ->post(
            route('projects.columns.store', $project),
            [
                'name' => 'To Do',
            ]
        );

    $response->assertRedirect();

    $this->assertDatabaseHas('board_columns', [
        'project_id' => $project->id,
        'name' => 'To Do',
    ]);
});

test('user can rename a board column', function () {
    $company = Company::factory()->create();

    $user = User::factory()->create([
        'company_id' => $company->id,
    ]);

    $project = Project::factory()->create([
        'company_id' => $company->id,
    ]);

    $column = BoardColumn::factory()->create([
        'project_id' => $project->id,
        'name' => 'To Do',
    ]);

    $response = $this
        ->actingAs($user)
        ->put(
            route(
                'projects.columns.update',
                [$project, $column]
            ),
            [
                'name' => 'Backlog',
            ]
        );

    $response->assertRedirect();

    $this->assertDatabaseHas('board_columns', [
        'id' => $column->id,
        'name' => 'Backlog',
    ]);
});

test('user can delete a board column', function () {
    $company = Company::factory()->create();

    $user = User::factory()->create([
        'company_id' => $company->id,
    ]);

    $project = Project::factory()->create([
        'company_id' => $company->id,
    ]);

    $column = BoardColumn::factory()->create([
        'project_id' => $project->id,
    ]);

    $response = $this
        ->actingAs($user)
        ->delete(
            route(
                'projects.columns.destroy',
                [$project, $column]
            )
        );

    $response->assertRedirect();

    $this->assertDatabaseMissing('board_columns', [
        'id' => $column->id,
    ]);
});

test('user can reorder board columns', function () {
    $company = Company::factory()->create();

    $user = User::factory()->create([
        'company_id' => $company->id,
    ]);

    $project = Project::factory()->create([
        'company_id' => $company->id,
    ]);

    $todo = BoardColumn::factory()->create([
        'project_id' => $project->id,
        'name' => 'To Do',
        'position' => 1,
    ]);

    $progress = BoardColumn::factory()->create([
        'project_id' => $project->id,
        'name' => 'In Progress',
        'position' => 2,
    ]);

    $this
        ->actingAs($user)
        ->patch(
            route(
                'projects.columns.reorder',
                [$project, $todo, 'right']
            )
        )
        ->assertRedirect();

    expect($todo->fresh()->position)->toBe(2);
    expect($progress->fresh()->position)->toBe(1);
});