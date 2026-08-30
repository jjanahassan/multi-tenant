<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreProjectRequest;
use App\Http\Requests\UpdateProjectRequest;
use App\Models\Project;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use App\Models\User;
use Illuminate\Http\Request;

class ProjectController extends Controller
{
    use AuthorizesRequests;
    
    /**
     * Display a listing of projects.
     */
    public function index()
    {
        $this->authorize('viewAny', Project::class);

        $projects = Project::latest()->get();

        return view('projects.index', compact('projects'));
    }

    /**
     * Show the form for creating a project.
     */
    public function create()
    {
        $this->authorize('create', Project::class);

        return view('projects.create');
    }

    /**
     * Store a newly created project.
     */
    public function store(StoreProjectRequest $request)
    {
        $project = Project::create([
            'company_id' => auth()->user()->company_id,
            'name' => $request->validated('name'),
            'description' => $request->validated('description'),
        ]);

        return redirect()
            ->route('projects.show', $project)
            ->with('success', 'Project created successfully.');
    }

    /**
     * Display the specified project.
     */

public function show(Request $request, Project $project)
    {
        $users = $project->company->users;

        $assigneeId = $request->input('assignee_id');
        $dueDate = $request->input('due_date');
        $sortDueDate = $request->input('sort_due_date');

        $project->load([
            'boardColumns.tasks' => function ($query) use (
                $assigneeId,
                $dueDate,
                $sortDueDate
            ) {

                /*
                * Filter by assignee
                */
                if ($assigneeId) {
                    $query->where('assignee_id', $assigneeId);
                }

                /*
                * Filter by exact due date
                */
                if ($dueDate) {
                    $query->whereDate('due_date', $dueDate);
                }

                /*
                * Sort by due date
                */
                if ($sortDueDate === 'asc') {
                    $query->orderByRaw(
                        'due_date IS NULL, due_date ASC'
                    );
                } elseif ($sortDueDate === 'desc') {
                    $query->orderByRaw(
                        'due_date IS NULL, due_date DESC'
                    );
                }

                /*
                * Always use position as the secondary ordering.
                */
                $query->orderBy('position');
            },
        ]);

        return view('projects.show', compact(
            'project',
            'users',
            'assigneeId',
            'dueDate',
            'sortDueDate'
        ));
    }

    /**
     * Update the specified project.
     */
    public function update(
        UpdateProjectRequest $request,
        Project $project
    ) {
        $project->update($request->validated());

        return redirect()
            ->route('projects.show', $project)
            ->with('success', 'Project updated successfully.');
    }

    /**
     * Remove the specified project.
     */
    public function destroy(Project $project)
    {
        $this->authorize('delete', $project);

        $project->delete();

        return redirect()
            ->route('projects.index')
            ->with('success', 'Project deleted successfully.');
    }
}