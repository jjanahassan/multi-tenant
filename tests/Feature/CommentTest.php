<?php

use App\Models\Comment;
use App\Models\Company;
use App\Models\Project;
use App\Models\Task;
use App\Models\User;
use App\Models\Activity;

test('user can comment on a task', function () {
    $company = Company::factory()->create();

    $user = User::factory()->create([
        'company_id' => $company->id,
        'role' => 'member',
    ]);

    $project = Project::factory()->create([
        'company_id' => $company->id,
    ]);

    $task = Task::factory()->create([
        'project_id' => $project->id,
    ]);

    $response = $this
        ->actingAs($user)
        ->post(
            route('tasks.comments.store', $task),
            [
                'body' => 'This task looks good.',
            ]
        );

    $response->assertRedirect();

    $this->assertDatabaseHas('comments', [
        'user_id' => $user->id,
        'commentable_type' => Task::class,
        'commentable_id' => $task->id,
        'body' => 'This task looks good.',
    ]);
});

test('user can delete their own comment', function () {
    $company = Company::factory()->create();

    $user = User::factory()->create([
        'company_id' => $company->id,
        'role' => 'member',
    ]);

    $project = Project::factory()->create([
        'company_id' => $company->id,
    ]);

    $task = Task::factory()->create([
        'project_id' => $project->id,
    ]);

    $comment = $task->comments()->create([
        'user_id' => $user->id,
        'body' => 'My comment.',
    ]);

    $response = $this
        ->actingAs($user)
        ->delete(
            route('comments.destroy', $comment)
        );

    $response->assertRedirect();

    $this->assertDatabaseMissing('comments', [
        'id' => $comment->id,
    ]);
});

test('user cannot comment on a task from another company', function () {
    $companyA = Company::factory()->create();
    $companyB = Company::factory()->create();

    $userA = User::factory()->create([
        'company_id' => $companyA->id,
        'role' => 'member',
    ]);

    $projectB = Project::factory()->create([
        'company_id' => $companyB->id,
    ]);

    $taskB = Task::factory()->create([
        'project_id' => $projectB->id,
    ]);

    $response = $this
        ->actingAs($userA)
        ->post(
            route('tasks.comments.store', $taskB),
            [
                'body' => 'Unauthorized comment.',
            ]
        );

    $response->assertForbidden();

    $this->assertDatabaseMissing('comments', [
        'commentable_id' => $taskB->id,
        'body' => 'Unauthorized comment.',
    ]);
});

test('comment belongs to the authenticated user', function () {
    $company = Company::factory()->create();

    $user = User::factory()->create([
        'company_id' => $company->id,
        'role' => 'member',
    ]);

    $project = Project::factory()->create([
        'company_id' => $company->id,
    ]);

    $task = Task::factory()->create([
        'project_id' => $project->id,
    ]);

    $this
        ->actingAs($user)
        ->post(
            route('tasks.comments.store', $task),
            [
                'body' => 'My comment.',
            ]
        );

    $comment = Comment::latest()->first();

    expect($comment->user_id)->toBe($user->id);
});

test('commenting through the HTTP endpoint creates notification and activity', function () {
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
        'assignee_id' => $assignee->id,
    ]);

    $this->actingAs($owner)
        ->post("/tasks/{$task->id}/comments", [
            'body' => 'This is an audit test comment.',
        ])
        ->assertSessionHasNoErrors();

    expect(
        Activity::where('task_id', $task->id)
            ->where('user_id', $owner->id)
            ->where('action', 'commented')
            ->exists()
    )->toBeTrue();

    expect(
        $assignee->notifications()
            ->where('type', \App\Notifications\TaskCommentedNotification::class)
            ->exists()
    )->toBeTrue();
});