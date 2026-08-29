<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class MoveTaskRequest extends FormRequest
{
    public function authorize(): bool
    {
        $project = $this->route('project');

        return $project
            && $this->user()
            && $this->user()->company_id === $project->company_id;
    }

    public function rules(): array
    {
        $project = $this->route('project');

        return [
            'board_column_id' => [
                'required',
                'integer',
                Rule::exists('board_columns', 'id')
                    ->where('project_id', $project?->id),
            ],

            'position' => [
                'required',
                'integer',
                'min:0',
            ],
        ];
    }
}