<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreBoardColumnRequest;
use App\Http\Requests\UpdateBoardColumnRequest;
use App\Models\BoardColumn;
use App\Models\Project;

class BoardColumnController extends Controller
{
    public function store(StoreBoardColumnRequest $request, Project $project){
        $nextPosition= $project->boardColumns()->max('position')+1;
        $project->boardColumns()->create([
            'name'=>$request->validated('name'), 
            'position'=> $nextPosition,
        ]);

        return back()->with('success', 'Board column added successfully.');
    }

    public function update(UpdateBoardColumnRequest $request, Project $project, BoardColumn $boardColumn){
        abort_unless($boardColumn->project_id=== $project->id, 404);
        $boardColumn->update($request->validated());

        return back()-> with('success', 'Board column updated successfully');
    }

    public function destroy(Project $project, BoardColumn $boardColumn){
        abort_unless($boardColumn->project_id=== $project->id, 404);
        $boardColumn->delete();

        return back()-> with('success', 'Board column deleted successfully');
    }

    public function reorder(Project $project, BoardColumn $boardColumn, string $direction) {
        abort_unless($boardColumn->project_id === $project->id, 404);

        $columns = $project->boardColumns()->orderBy('position')->get()->values();
        $currentIndex = $columns->search(fn ($column) => $column->id === $boardColumn->id);

        if ($currentIndex === false) {
            abort(404);
        }

        if ($direction === 'left' && $currentIndex > 0) {
            $otherColumn = $columns->get($currentIndex - 1);
            $this->swapPositions($boardColumn, $otherColumn);
        }

        if ($direction === 'right' && $currentIndex < $columns->count() - 1) {
            $otherColumn = $columns->get($currentIndex + 1);
            $this->swapPositions($boardColumn, $otherColumn);
        }

        return back()->with('success','Board column reordered successfully.');
    }

    private function swapPositions(BoardColumn $first, BoardColumn $second): void{
        $temp= $first->position;
        $first->update(['position'=> $second->position,]);
        $second->update(['position'=>$temp,]);
    }
}
