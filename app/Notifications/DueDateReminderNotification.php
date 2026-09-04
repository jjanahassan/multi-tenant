<?php

namespace App\Notifications;

use App\Models\Task;
use Illuminate\Notifications\Notification;

class DueDateReminderNotification extends Notification
{
    public function __construct(
        public Task $task
    ) {}

    public function id(): string
    {
        $key = sprintf(
            'due-date-reminder:%d:%d:%s',
            $this->task->id,
            $this->task->assignee_id,
            $this->task->due_date->toDateString()
        );

        $hash = md5($key);

        return sprintf(
            '%s-%s-%s-%s-%s',
            substr($hash, 0, 8),
            substr($hash, 8, 4),
            substr($hash, 12, 4),
            substr($hash, 16, 4),
            substr($hash, 20, 12)
        );
    }

    public function via(object $notifiable): array
    {
        return ['database'];
    }

    public function toDatabase(object $notifiable): array
    {
        return [
            'type' => 'task_due_soon',
            'task_id' => $this->task->id,
            'project_id' => $this->task->project_id,
            'task_title' => $this->task->title,
            'due_date' => $this->task->due_date?->toDateString(),
            'message' => 'This task is due within 24 hours.',
        ];
    }
}