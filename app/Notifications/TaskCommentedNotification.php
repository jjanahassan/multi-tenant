<?php

namespace App\Notifications;

use App\Models\Comment;
use App\Models\Task;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Notification;

class TaskCommentedNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(
        public Comment $comment
    ) {}

    /**
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['database'];
    }

    /**
     * @return array<string, mixed>
     */
    public function toDatabase(object $notifiable): array
    {
        $task = $this->comment->commentable;

        if (! $task instanceof Task) {
            throw new \LogicException('Comment must belong to a task.');
        }

        return [
            'type' => 'task_commented',
            'task_id' => $task->id,
            'project_id' => $task->project_id,
            'task_title' => $task->title,
            'message' => 'A new comment was added to your task.',
        ];
    }
}
