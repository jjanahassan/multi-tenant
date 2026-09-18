<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class TaskFilterRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user() !== null;
    }

    public function rules(): array
    {
        $project = $this->route('project');

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
                    ->where('company_id', $project?->company_id),
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
                    ->where('project_id', $project?->id),
            ],

            'sort_due_date' => [
                'nullable',
                Rule::in(['asc', 'desc']),
            ],
        ];
    }
}