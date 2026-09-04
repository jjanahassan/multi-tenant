<?php

use App\Jobs\SendDueDateReminder;
use App\Models\Company;
use App\Models\Project;
use App\Models\Task;
use App\Models\User;
use Illuminate\Support\Facades\Queue;

test('due soon assigned tasks are dispatched to the queue', function () {
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

    $dueSoonTask = Task::factory()->create([
        'project_id' => $project->id,
        'assignee_id' => $assignee->id,
        'due_date' => today(),
    ]);

    $futureTask = Task::factory()->create([
        'project_id' => $project->id,
        'assignee_id' => $assignee->id,
        'due_date' => today()->addDays(5),
    ]);

    $this->artisan('tasks:send-due-date-reminders')
        ->assertSuccessful();

    Queue::assertPushed(
        SendDueDateReminder::class,
        1
    );

    Queue::assertPushed(
        SendDueDateReminder::class,
        function (SendDueDateReminder $job) use ($dueSoonTask) {
            return $job->task->id === $dueSoonTask->id;
        }
    );
});

test('unassigned due soon tasks are not dispatched', function () {
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
        ->assertSuccessful();

    Queue::assertNotPushed(
        SendDueDateReminder::class
    );
});