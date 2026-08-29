<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateTaskRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
         $project= $this->route('project');

        return $project && $this->user() && $this->user()->company_id === $project->company_id;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
       $project = $this->route('project');

        return [
            'title'=> ['required', 'string', 'max:255', ],

            'description'=> ['nullable', 'string', ],

            'board_column_id'=> ['required', 'integer',
            Rule::exists('board_columns', 'id')-> where('project_id', $project?->id), ],

            'assignee_id'=> ['nullable', 'integer', 
            Rule::exists('users', 'id')-> where('company_id', $project?->company_id), ],

            'due_date'=> ['nullable', 'date', ],
        ]; 
    }
}
