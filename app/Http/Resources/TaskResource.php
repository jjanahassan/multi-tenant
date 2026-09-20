<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use App\Models\Task;

class TaskResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        /** @var Task $task */
        $task = $this->resource;

        return [
            'id' => $task->id,
            'title' => $task->title,
            'description' => $task->description,
            'project_id' => $task->project_id,
            'board_column_id' => $task->board_column_id,
            'assignee_id' => $task->assignee_id,
            'due_date' => $task->due_date?->toDateString(),
            'position' => $task->position,
            'created_at' => $task->created_at,
            'updated_at' => $task->updated_at,
        ];
    }
}