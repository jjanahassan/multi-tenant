<?php

use App\Events\CommentAdded;
use App\Events\TaskAssigned;
use App\Models\Comment;
use App\Models\Company;
use App\Models\Project;
use App\Models\Task;
use App\Models\User;
use App\Notifications\TaskAssignedNotification;
use App\Notifications\TaskCommentedNotification;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Str;

uses(RefreshDatabase::class);

function createNotificationTestCompany(): array
{
    $owner = User::factory()->create();

    $company = Company::factory()->create([
        'owner_id' => $owner->id,
    ]);

    $owner->update([
        'company_id' => $company->id,
        'role' => 'owner',
    ]);

    return [$company, $owner];
}

test('assigned user receives a task assignment notification', function () {
    Notification::fake();

    [$company, $owner] = createNotificationTestCompany();

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

    TaskAssigned::dispatch(
        $task,
        $owner
    );

    Notification::assertSentTo(
        $assignee,
        TaskAssignedNotification::class
    );
});

test('task assignee receives a notification when someone comments', function () {
    Notification::fake();

    [$company, $owner] = createNotificationTestCompany();

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

    $comment = Comment::factory()->create([
        'user_id' => $owner->id,
        'commentable_id' => $task->id,
        'commentable_type' => Task::class,
    ]);

    CommentAdded::dispatch(
        $comment,
        $owner
    );

    Notification::assertSentTo(
        $assignee,
        TaskCommentedNotification::class
    );
});

test('commenter does not receive a notification for their own comment', function () {
    Notification::fake();

    [$company, $owner] = createNotificationTestCompany();

    $project = Project::factory()->create([
        'company_id' => $company->id,
    ]);

    $task = Task::factory()->create([
        'project_id' => $project->id,
        'assignee_id' => $owner->id,
    ]);

    $comment = Comment::factory()->create([
        'user_id' => $owner->id,
        'commentable_id' => $task->id,
        'commentable_type' => Task::class,
    ]);

    CommentAdded::dispatch(
        $comment,
        $owner
    );

    Notification::assertNotSentTo(
        $owner,
        TaskCommentedNotification::class
    );
});

test('notification can be marked as read', function () {
    $user = User::factory()->create();

    $notification = $user->notifications()->create([
        'id' => Str::uuid(),
        'type' => TaskAssignedNotification::class,
        'data' => [
            'task_id' => 1,
            'task_title' => 'Test Task',
            'message' => 'You have been assigned to a task.',
        ],
    ]);

    expect($notification->read_at)->toBeNull();

    $this->actingAs($user)
        ->post(route('notifications.read', $notification->id))
        ->assertRedirect();

    expect(
        $user->notifications()
            ->find($notification->id)
            ->read_at
    )->not->toBeNull();
});

test('user can retrieve unread notifications', function () {
    $user = User::factory()->create();

    $unreadNotification = $user->notifications()->create([
        'id' => Str::uuid(),
        'type' => TaskAssignedNotification::class,
        'data' => [
            'task_id' => 1,
            'task_title' => 'Test Task',
            'message' => 'You have been assigned to a task.',
        ],
    ]);

    $readNotification = $user->notifications()->create([
        'id' => Str::uuid(),
        'type' => TaskAssignedNotification::class,
        'data' => [
            'task_id' => 2,
            'task_title' => 'Another Task',
            'message' => 'You have been assigned to a task.',
        ],
        'read_at' => now(),
    ]);

    expect($user->unreadNotifications)
    ->toHaveCount(1)
    ->and((string) $user->unreadNotifications->first()->id)
    ->toBe((string) $unreadNotification->id);
});