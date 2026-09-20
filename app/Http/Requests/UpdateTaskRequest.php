<?php

namespace App\Http\Requests;

use App\Models\Project;
use App\Models\Task;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateTaskRequest extends FormRequest
{
    public function authorize(): bool
    {
        $task = $this->route('task');

        if (! $task instanceof Task || ! $this->user()) {
            return false;
        }

        $project = $this->route('project');

        if (! $project instanceof Project) {
            $project = $task->project;
        }

        return $project instanceof Project
            && $this->user()->company_id === $project->company_id;
    }

    /** @return array<string, ValidationRule|array<mixed>|string> */
    public function rules(): array
    {
        $task = $this->route('task');

        $project = $this->route('project');

        if (! $project instanceof Project) {
            $project = $task instanceof Task ? $task->project : null;
        }

        return [
            'title' => [
                'required',
                'string',
                'max:255',
            ],

            'description' => [
                'nullable',
                'string',
            ],

            'board_column_id' => [
                'required',
                'integer',
                Rule::exists('board_columns', 'id')
                    ->where(
                        'project_id',
                        $project instanceof Project ? $project->id : null
                    ),
            ],

            'assignee_id' => [
                'nullable',
                'integer',
                Rule::exists('users', 'id')
                    ->where(
                        'company_id',
                        $project instanceof Project ? $project->company_id : null
                    ),
            ],

            'due_date' => [
                'nullable',
                'date',
            ],
        ];
    }
}