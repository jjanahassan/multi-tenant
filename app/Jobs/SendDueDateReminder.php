<?php

namespace App\Jobs;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use App\Models\Task;
use App\Notifications\DueDateReminderNotification;
use App\Models\DueDateReminder;
use Illuminate\Support\Facades\Log;
use Throwable;

class SendDueDateReminder implements ShouldQueue
{
    use Queueable;

    public int $tries = 3;
    public int $timeout = 60;

    /**
     * Create a new job instance.
     */
    public function __construct(
        public Task $task
    )
    {}

    public function backoff(): array{
        return [60, 300, 900];
    }

    public function failed(?Throwable $exception): void
    {
        Log::error('Due date reminder job failed permanently.', [
            'task_id' => $this->task->id,
            'assignee_id' => $this->task->assignee_id,
            'exception' => $exception?->getMessage(),
        ]);
    }
    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $this->task->load('assignee');

        if (!$this->task->assignee || !$this->task->due_date) {
            return;
        }

        $dueDate = $this->task->due_date->toDateString();

        $reminder = DueDateReminder::where('task_id', $this->task->id)
            ->where('assignee_id', $this->task->assignee_id)
            ->whereDate('due_date', $dueDate)
            ->first();

        if ($reminder?->sent_at !== null) {
            return;
        }

        if (!$reminder) {
            try {
                $reminder = DueDateReminder::create([
                    'task_id' => $this->task->id,
                    'assignee_id' => $this->task->assignee_id,
                    'due_date' => $dueDate,
                    'sent_at' => null,
                ]);
            } catch (\Illuminate\Database\UniqueConstraintViolationException) {
                $reminder = DueDateReminder::where('task_id', $this->task->id)
                    ->where('assignee_id', $this->task->assignee_id)
                    ->whereDate('due_date', $dueDate)
                    ->first();

                if ($reminder?->sent_at !== null) {
                    return;
                }
            }
        }

        $notification = new DueDateReminderNotification($this->task);

        $existingNotification = $this->task->assignee
            ->notifications()
            ->where('id', $notification->id())
            ->exists();

        if ($existingNotification) {
            $reminder->update([
                'sent_at' => now(),
            ]);

            return;
        }

        $this->task->assignee->notify($notification);

        $reminder->update([
            'sent_at' => now(),
        ]);
    }
}
