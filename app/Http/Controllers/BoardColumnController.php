<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreBoardColumnRequest;
use App\Http\Requests\UpdateBoardColumnRequest;
use App\Models\BoardColumn;
use App\Models\Project;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class BoardColumnController extends Controller
{
    use AuthorizesRequests;
    
    public function store(
        StoreBoardColumnRequest $request,
        Project $project
    ) {
        $nextPosition = ($project->boardColumns()->max('position') ?? -1) + 1;

        $project->boardColumns()->create([
            'name' => $request->validated('name'),
            'position' => $nextPosition,
        ]);

        return back()->with(
            'success',
            'Board column added successfully.'
        );
    }

    public function update(
        UpdateBoardColumnRequest $request,
        Project $project,
        BoardColumn $boardColumn
    ) {
        $boardColumn->update($request->validated());

        return back()->with(
            'success',
            'Board column updated successfully.'
        );
    }

    public function destroy(
        Project $project,
        BoardColumn $boardColumn
    ) {
        $this->authorize('update', $project);

        $boardColumn->delete();

        return back()->with(
            'success',
            'Board column deleted successfully.'
        );
    }

    public function reorder(
        Project $project,
        BoardColumn $boardColumn,
        string $direction
    ) {
        $this->authorize('update', $project);

        abort_unless(
            in_array($direction, ['left', 'right']),
            422
        );

        $columns = $project
            ->boardColumns()
            ->orderBy('position')
            ->get()
            ->values();

        $currentIndex = $columns->search(
            fn ($column) => $column->id === $boardColumn->id
        );

        if ($direction === 'left' && $currentIndex > 0) {
            $otherColumn = $columns->get($currentIndex - 1);

            $this->swapPositions(
                $boardColumn,
                $otherColumn
            );
        }

        if (
            $direction === 'right'
            && $currentIndex < $columns->count() - 1
        ) {
            $otherColumn = $columns->get($currentIndex + 1);

            $this->swapPositions(
                $boardColumn,
                $otherColumn
            );
        }

        return back()->with(
            'success',
            'Board column reordered successfully.'
        );
    }

    private function swapPositions(
        BoardColumn $first,
        BoardColumn $second
    ): void {
        $temp = $first->position;

        $first->update([
            'position' => $second->position,
        ]);

        $second->update([
            'position' => $temp,
        ]);
    }
}