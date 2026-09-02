<?php

namespace App\Listeners;

use App\Events\TaskAssigned;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use App\Notifications\TaskAssignedNotification;

class SendTaskAssignedNotification
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
    public function handle(TaskAssigned $event): void
    {
        $assignee = $event->task->assignee;

        if(!$assignee){
            return;
        }

        if($assignee->id === $event->user->id){
            return;
        }

        $assignee->notify(new TaskAssignedNotification($event->task));
    }
}
