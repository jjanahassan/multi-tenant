<?php

use App\Jobs\SendDueDateReminder;
use App\Models\Company;
use App\Models\Project;
use App\Models\Task;
use App\Models\User;
use Illuminate\Support\Facades\Queue;

test('command dispatches reminders for tasks due today', function () {
    Queue::fake();

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

    $this->artisan('tasks:send-due-date-reminders')
        ->assertExitCode(0);

    Queue::assertPushed(
        SendDueDateReminder::class,
        fn ($job) => $job->task->id === $task->id
    );
});

test('command dispatches reminders for tasks due tomorrow', function () {
    Queue::fake();

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
        'due_date' => today()->addDay(),
    ]);

    $this->artisan('tasks:send-due-date-reminders')
        ->assertExitCode(0);

    Queue::assertPushed(
        SendDueDateReminder::class,
        fn ($job) => $job->task->id === $task->id
    );
});

test('command ignores tasks without an assignee', function () {
    Queue::fake();

    $owner = User::factory()->create();

    $company = Company::factory()->create([
        'owner_id' => $owner->id,
    ]);

    $owner->update([
        'company_id' => $company->id,
        'role' => 'owner',
    ]);

    $project = Project::factory()->create([
        'company_id' => $company->id,
    ]);

    Task::factory()->create([
        'project_id' => $project->id,
        'assignee_id' => null,
        'due_date' => today(),
    ]);

    $this->artisan('tasks:send-due-date-reminders')
        ->assertExitCode(0);

    Queue::assertNothingPushed();
});

test('command ignores tasks without a due date', function () {
    Queue::fake();

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

    Task::factory()->create([
        'project_id' => $project->id,
        'assignee_id' => $assignee->id,
        'due_date' => null,
    ]);

    $this->artisan('tasks:send-due-date-reminders')
        ->assertExitCode(0);

    Queue::assertNothingPushed();
});

test('command does not dispatch tasks outside the due soon window', function () {
    Queue::fake();

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

    Task::factory()->create([
        'project_id' => $project->id,
        'assignee_id' => $assignee->id,
        'due_date' => today()->addDays(3),
    ]);

    $this->artisan('tasks:send-due-date-reminders')
        ->assertExitCode(0);

    Queue::assertNothingPushed();
});