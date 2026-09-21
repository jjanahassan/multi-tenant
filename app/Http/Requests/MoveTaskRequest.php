<?php

namespace App\Http\Requests;

use App\Models\Project;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class MoveTaskRequest extends FormRequest
{
    public function authorize(): bool
    {
        $project = $this->route('project');

        return $project instanceof Project
            && $this->user()
            && $this->user()->company_id === $project->company_id;
    }

    /** @return array<string, mixed> */
    public function rules(): array
    {
        $project = $this->route('project');

        return [
            'board_column_id' => [
                'required',
                'integer',
                Rule::exists('board_columns', 'id')
                    ->where(
                        'project_id',
                        $project instanceof Project ? $project->id : null
                    ),
            ],
            'position' => [
                'required',
                'integer',
                'min:0',
            ],
        ];
    }
}
