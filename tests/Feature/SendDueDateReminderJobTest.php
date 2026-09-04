<?php

use App\Jobs\SendDueDateReminder;
use App\Models\Company;
use App\Models\Project;
use App\Models\Task;
use App\Models\User;
use App\Models\DueDateReminder;
use App\Notifications\DueDateReminderNotification;

test('due date reminder job is idempotent', function () {
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
        'due_date' => today(),
    ]);

    $job = new SendDueDateReminder($task);

    $job->handle();
    $job->handle();

    expect(
        $assignee->notifications()
            ->where('type', DueDateReminderNotification::class)
            ->count()
    )->toBe(1);

    expect(
        DueDateReminder::where('task_id', $task->id)->count()
    )->toBe(1);

    expect(
        DueDateReminder::where('task_id', $task->id)
            ->first()
            ->sent_at
    )->not->toBeNull();
});

test('due date reminder job has sensible retry configuration', function () {
    $task = Task::factory()->create();

    $job = new SendDueDateReminder($task);

    expect($job->tries)->toBe(3)
        ->and($job->timeout)->toBe(60)
        ->and($job->backoff())->toBe([60, 300, 900]);
});
