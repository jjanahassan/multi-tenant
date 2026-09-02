<?php

namespace App\Listeners;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use App\Events\CommentAdded;
use App\Events\TaskAssigned;
use App\Events\TaskCreated;
use App\Events\TaskMoved;
use App\Models\Activity;

class LogTaskActivity
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
    public function handle(TaskCreated|TaskMoved|TaskAssigned|CommentAdded $event): void
    {
        if ($event instanceof TaskCreated){
            Activity::create([
                'user_id'=> $event->user->id,
                'task_id'=> $event->task->id,
                'action'=>'created',
                'description'=>'Task was created.',
            ]);
            return;
        }

        if ($event instanceof TaskMoved){
            Activity::create([
                'user_id'=> $event->user->id,
                'task_id'=> $event->task->id,
                'action'=>'moved',
                'description'=>'Task was moved to another column.',
            ]);
            return;
        }

        if ($event instanceof TaskAssigned){
            Activity::create([
                'user_id'=> $event->user->id,
                'task_id'=> $event->task->id,
                'action'=>'assigned',
                'description'=>'Task was assigned to a teammate.',
            ]);
            return;
        }

        if ($event instanceof CommentAdded) {
            $task = $event->comment->commentable;

            Activity::create([
                'task_id' => $task->id,
                'user_id' => $event->user->id,
                'action' => 'commented',
                'description' => 'A comment was added.',
            ]);
        }
    }
}
