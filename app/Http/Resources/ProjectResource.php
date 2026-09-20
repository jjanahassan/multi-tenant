<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use App\Models\Project;

class ProjectResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        /** @var Project $project */
        $project = $this->resource;

        return [
            'id' => $project->id,
            'name' => $project->name,
            'description' => $project->description,
            'company_id' => $project->company_id,
            'created_at' => $project->created_at,
            'updated_at' => $project->updated_at,
        ];
    }
}