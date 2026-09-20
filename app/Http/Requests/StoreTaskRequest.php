<?php

namespace App\Http\Requests;

use App\Models\Project;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreTaskRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        $project = $this->route('project');

        if (! $project instanceof Project) {
            return false;
        }

        return $this->user() && $this->user()->company_id === $project->company_id;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
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
            'title' => ['required', 'string', 'max:255'],

            'description' => ['nullable', 'string'],

            'board_column_id' => ['required', 'integer',
                Rule::exists('board_columns', 'id')->where('project_id', $projectId), ],

            'assignee_id' => ['nullable', 'integer',
                Rule::exists('users', 'id')->where('company_id', $companyId), ],

            'due_date' => ['nullable', 'date'],
        ];
    }
}
