<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use App\Models\Project;

class TaskFilterRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user() !== null;
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        $project = $this->route('project');

        $projectId = $project instanceof Project
            ? $project->id
            : null;

        $companyId = $project instanceof Project
            ? $project->company_id
            : null;

        return [
            'search' => [
                'nullable',
                'string',
                'max:255',
            ],

            'assignee_id' => [
                'nullable',
                'integer',
                Rule::exists('users', 'id')
                    ->where('company_id', $companyId),
            ],

            'due_from' => [
                'nullable',
                'date',
            ],

            'due_to' => [
                'nullable',
                'date',
                'after_or_equal:due_from',
            ],

            'column_id' => [
                'nullable',
                'integer',
                Rule::exists('board_columns', 'id')
                    ->where('project_id', $projectId),
            ],

            'sort_due_date' => [
                'nullable',
                Rule::in(['asc', 'desc']),
            ],
        ];
    }
}