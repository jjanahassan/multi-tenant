<?php

namespace Database\Factories;

use App\Models\BoardColumn;
use App\Models\Project;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Task>
 */
class TaskFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'project_id'=> Project::factory(),
            'board_column_id'=> BoardColumn::factory(),
            'assignee_id'=> null,
            'title'=>fake()->sentence(3),
            'description'=>fake()->optional()->paragraph(),
            'due_date'=>fake()->optional()->date(),
            'position'=>fake()->numberBetween(0,10),
        ];
    }
}
