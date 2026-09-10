<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreProjectRequest;
use App\Http\Requests\UpdateProjectRequest;
use App\Http\Resources\ProjectResource;
use App\Models\Project;
use Illuminate\Http\Request;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class ProjectController extends Controller
{
    use AuthorizesRequests;
    
    /**
     * Display a listing of the authenticated user's projects.
     */
    public function index()
    {
        $this->authorize('viewAny', Project::class);

        $projects = Project::latest()->get();

        return ProjectResource::collection($projects);
    }

    /**
     * Store a newly created project.
     */
    public function store(StoreProjectRequest $request)
    {
        $project = Project::create([
            'company_id' => $request->user()->company_id,
            'name' => $request->validated('name'),
            'description' => $request->validated('description'),
        ]);

        return (new ProjectResource($project))
            ->response()
            ->setStatusCode(201);
    }

    /**
     * Display the specified project.
     */
    public function show(Project $project)
    {
        $this->authorize('view', $project);

        return new ProjectResource($project);
    }

    /**
     * Update the specified project.
     */
    public function update(
        UpdateProjectRequest $request,
        Project $project
    ) {
        $project->update($request->validated());

        return new ProjectResource($project->fresh());
    }

    /**
     * Remove the specified project.
     */
    public function destroy(Project $project)
    {
        $this->authorize('delete', $project);

        $project->delete();

        return response()->json([
            'message' => 'Project deleted successfully.',
        ]);
    }
}