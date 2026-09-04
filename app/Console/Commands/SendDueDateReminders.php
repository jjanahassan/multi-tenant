<?php

namespace App\Console\Commands;

use App\Jobs\SendDueDateReminder;
use App\Models\Task;
use Illuminate\Console\Command;

class SendDueDateReminders extends Command
{
    protected $signature = 'tasks:send-due-date-reminders';

    protected $description = 'Dispatch reminders for tasks due within 24 hours';

    public function handle(): int
    {
        $today = now()->startOfDay();
        $tomorrow = now()->addDay()->startOfDay();

        $tasks = Task::query()
            ->whereNotNull('due_date')
            ->whereNotNull('assignee_id')
            ->whereBetween('due_date', [$today, $tomorrow])
            ->get();

        foreach ($tasks as $task) {
            SendDueDateReminder::dispatch($task);
        }

        $this->info(
            "Dispatched {$tasks->count()} due date reminder job(s)."
        );

        return self::SUCCESS;
    }
}