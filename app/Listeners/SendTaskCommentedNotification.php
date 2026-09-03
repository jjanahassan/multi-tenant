<?php

namespace App\Listeners;

use App\Events\CommentAdded;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use App\Notifications\TaskCommentedNotification;


class SendTaskCommentedNotification
{
    /**
     * Create the event listener.
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     */
    public function handle(CommentAdded $event): void
    {
        $task = $event->comment->commentable;

        if (!$task) {
            return;
        }

        $assignee = $task->assignee;

        if (!$assignee) {
            return;
        }

        if ($assignee->id === $event->user->id) {
            return;
        }

        $assignee->notify(
            new TaskCommentedNotification($event->comment)
        );
    }
}
