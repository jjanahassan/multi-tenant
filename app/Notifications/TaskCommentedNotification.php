<?php

namespace App\Notifications;

use App\Models\Comment;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Notification;

class TaskCommentedNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(
        public Comment $comment
    ) {}

    public function via(object $notifiable): array
    {
        return ['database'];
    }

    public function toDatabase(object $notifiable): array
    {
        $task = $this->comment->commentable;

        return [
            'type' => 'task_commented',
            'task_id' => $task->id,
            'project_id' => $task->project_id,
            'task_title' => $task->title,
            'message' => 'A new comment was added to your task.',
        ];
    }
}