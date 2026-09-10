<?php

namespace App\Policies;

use App\Models\Task;
use App\Models\User;

class TaskPolicy
{
    public function view(User $user, Task $task): bool
    {
        return $task->project !== null
            && $user->company_id === $task->project->company_id;
    }

    public function create(User $user): bool
    {
        return $user->company_id !== null
            && in_array($user->role, ['owner', 'admin']);
    }

    public function update(User $user, Task $task): bool
    {
        return $task->project !== null
            && $user->company_id === $task->project->company_id
            && in_array($user->role, ['owner', 'admin']);
    }

    public function delete(User $user, Task $task): bool
    {
        return $task->project !== null
            && $user->company_id === $task->project->company_id
            && in_array($user->role, ['owner', 'admin']);
    }
}