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
}
