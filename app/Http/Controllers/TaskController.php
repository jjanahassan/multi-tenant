<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreTaskRequest;
use App\Http\Requests\UpdateTaskRequest;
use App\Models\Project;
use App\Models\Task;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use App\Http\Requests\MoveTaskRequest;
use Illuminate\Support\Facades\DB;

class TaskController extends Controller
{
    use AuthorizesRequests;

    public function store(StoreTaskRequest $request, Project $project){
        
        $validated= $request-> validated();

        $nextPosition= (
            $project
            ->tasks()
            ->where('board_column_id', $validated['board_column_id'])
            ->max('position')
            ?? -1
        ) +1;

        $project->tasks()->create([
            'board_column_id'=> $validated['board_column_id'],
            'assignee_id'=> $validated['assignee_id']?? null,
            'title'=> $validated['title'],
            'description'=> $validated['description']?? null,
            'due_date'=>$validated['due_date']?? null,
            'position'=> $nextPosition,
        ]);

        return back()-> with('success', 'Task created successfully,');
    }

    public function update(UpdateTaskRequest $request, Project $project, Task $task){
        abort_unless($task->project_id === $project->id, 404);

        $task->update($request->validated());

        return back()-> with('success', 'Task updated successfully.');
    }

    public function destroy(Project $project, Task $task){
        abort_unless($task->project_id === $project->id, 404);

        $this->authorize('update', $project);
        $task->delete();

        return back()-> with('success', 'Task deleted successfully.');
    }

    public function move(MoveTaskRequest $request, Project $project, Task $task) {
        abort_unless($task->project_id === $project->id, 404);

        $validated = $request->validated();

        DB::transaction(function () use ($task, $validated) {
            $oldColumnId = $task->board_column_id;
            $newColumnId = $validated['board_column_id'];
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

        return response()->json([
            'success' => true,
        ]);
    }
}
