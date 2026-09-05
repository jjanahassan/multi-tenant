<?php

use App\Models\Activity;
use App\Models\Company;
use App\Models\Project;
use App\Models\Task;
use App\Models\User;
use App\Models\Comment;
use App\Events\CommentAdded;
use App\Events\TaskAssigned;

test('task creation creates an activity record', function () {
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

    $this->actingAs($user);

    event(new \App\Events\TaskCreated($task, $user));

    expect(
        Activity::where('task_id', $task->id)
            ->where('user_id', $user->id)
            ->where('action', 'created')
            ->exists()
    )->toBeTrue();
});

test('task assignment creates an activity record', function () {
    $owner = User::factory()->create();

    $company = Company::factory()->create([
        'owner_id' => $owner->id,
    ]);

    $owner->update([
        'company_id' => $company->id,
        'role' => 'owner',
    ]);

    $assignee = User::factory()->create([
        'company_id' => $company->id,
        'role' => 'member',
    ]);

    $project = Project::factory()->create([
        'company_id' => $company->id,
    ]);

    $task = Task::factory()->create([
        'project_id' => $project->id,
        'assignee_id' => null,
    ]);

    $this->actingAs($owner);

    event(new \App\Events\TaskAssigned(
        $task,
        $owner,
        $assignee->id
    ));

    expect(
        Activity::where('task_id', $task->id)
            ->where('user_id', $owner->id)
            ->where('action', 'assigned')
            ->exists()
    )->toBeTrue();
});

test('comment activity is created', function () {
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

    $this->actingAs($user);

    $comment = $task->comments()->create([
        'user_id' => $user->id,
        'body' => 'Test comment',
    ]);

    event(new CommentAdded(
        $comment,
        $user
    ));

    expect(
        Activity::where('task_id', $task->id)
            ->where('user_id', $user->id)
            ->where('action', 'commented')
            ->exists()
    )->toBeTrue();
});