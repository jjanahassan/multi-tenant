<?php

use App\Http\Resources\CompanyResource;
use App\Http\Resources\ProjectResource;
use App\Http\Resources\TaskResource;
use App\Models\Company;
use App\Models\Project;
use App\Models\Task;
use App\Models\User;

test('company resource exposes the expected fields', function () {
    $user = User::factory()->create();

    $company = Company::factory()->create([
        'owner_id' => $user->id,
    ]);

    $user->update([
        'company_id' => $company->id,
        'role' => 'owner',
    ]);

    $response = (new CompanyResource($company))
        ->response()
        ->getData(true);

    expect($response)
        ->toHaveKey('data');

    expect($response['data'])
        ->toHaveKeys([
            'id',
            'name',
            'is_active',
        ]);
});

test('project resource exposes the expected fields', function () {
    $user = User::factory()->create();

    $company = Company::factory()->create([
        'owner_id' => $user->id,
    ]);

    $user->update([
        'company_id' => $company->id,
        'role' => 'owner',
    ]);

    $project = Project::factory()->create([
        'company_id' => $company->id,
    ]);

    $response = (new ProjectResource($project))
        ->response()
        ->getData(true);

    expect($response)
        ->toHaveKey('data');

    expect($response['data'])
        ->toHaveKeys([
            'id',
            'name',
            'company_id',
            'created_at',
            'updated_at',
        ]);
});

test('task resource exposes the expected fields', function () {
    $user = User::factory()->create();

    $company = Company::factory()->create([
        'owner_id' => $user->id,
    ]);

    $user->update([
        'company_id' => $company->id,
        'role' => 'owner',
    ]);

    $project = Project::factory()->create([
        'company_id' => $company->id,
    ]);

    $task = Task::factory()->create([
        'project_id' => $project->id,
    ]);

    $response = (new TaskResource($task))
        ->response()
        ->getData(true);

    expect($response)
        ->toHaveKey('data');

    expect($response['data'])
        ->toHaveKeys([
            'id',
            'title',
            'description',
            'project_id',
            'board_column_id',
            'assignee_id',
            'due_date',
            'position',
            'created_at',
            'updated_at',
        ]);
});