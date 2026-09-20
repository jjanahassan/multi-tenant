<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreTaskRequest;
use App\Http\Requests\UpdateTaskRequest;
use App\Models\Project;
use App\Models\Task;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use App\Http\Requests\MoveTaskRequest;
use Illuminate\Support\Facades\DB;
use App\Events\TaskCreated;
use App\Events\TaskAssigned;
use App\Events\TaskMoved;
use App\Services\PositionCalculator;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;

class TaskController extends Controller
{
    use AuthorizesRequests;

    public function store(StoreTaskRequest $request, Project $project): RedirectResponse
    {
        $validated = $request->validated();

        $maxPosition = $project
            ->tasks()
            ->where('board_column_id', $validated['board_column_id'])
            ->max('position');

        $nextPosition = (new PositionCalculator())
            ->nextPosition($maxPosition);

        $task = $project->tasks()->create([
            'board_column_id' => $validated['board_column_id'],
            'assignee_id' => $validated['assignee_id'] ?? null,
            'title' => $validated['title'],
            'description' => $validated['description'] ?? null,
            'due_date' => $validated['due_date'] ?? null,
            'position' => $nextPosition,
        ]);

        TaskCreated::dispatch($task, auth()->user());

        if ($task->assignee_id) {
            $task->refresh();
            $task->load('assignee');

            TaskAssigned::dispatch(
                $task,
                auth()->user(),
                null
            );
        }

        return back()->with('success', 'Task created successfully.');
    }

    public function update(UpdateTaskRequest $request, Project $project, Task $task): RedirectResponse {
        abort_unless($task->project_id === $project->id, 404);

        $previousAssigneeId = $task->assignee_id;
        $task->update($request->validated());

        if ($previousAssigneeId !== $task->assignee_id) {
            $task->refresh();
            $task->load('assignee');

            TaskAssigned::dispatch(
                $task,
                auth()->user(),
                $previousAssigneeId
            );
        }

        return back()-> with('success', 'Task updated successfully.');
    }

    public function destroy(Project $project, Task $task): RedirectResponse {
        abort_unless($task->project_id === $project->id, 404);

        $this->authorize('update', $project);
        $task->delete();

        return back()-> with('success', 'Task deleted successfully.');
    }

    public function move(MoveTaskRequest $request, Project $project, Task $task): JsonResponse {
        abort_unless($task->project_id === $project->id, 404);

        $validated = $request->validated();
        $oldColumnId = $task->board_column_id;
        $newColumnId = $validated['board_column_id'];

        DB::transaction(function () use ($task, $validated, $oldColumnId, $newColumnId) {
            
            $newPosition = $validated['position'];

            if ($oldColumnId !== $newColumnId) {
                Task::where('board_column_id', $oldColumnId)
                    ->where('position', '>', $task->position)
                    ->decrement('position');
            }

            $task->update([
                'board_column_id' => $newColumnId,
            ]);

            Task::where('board_column_id', $newColumnId)
                ->where('id', '!=', $task->id)
                ->where('position', '>=', $newPosition)
                ->increment('position');

            $task->update([
                'position' => $newPosition,
            ]);
        });

        TaskMoved::dispatch($task->fresh(), auth()->user(), $oldColumnId, $newColumnId);

        return response()->json([
            'success' => true,
            'message' => 'Task moved successfully.',
        ]);
    }
}
