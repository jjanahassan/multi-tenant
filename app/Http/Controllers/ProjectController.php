<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreProjectRequest;
use App\Http\Requests\UpdateProjectRequest;
use App\Models\Project;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use App\Models\User;
use App\Http\Requests\TaskFilterRequest;

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
     * Show the form for editing the specified project.
     */
    public function edit(Project $project)
    {
        $this->authorize('update', $project);

        return view('projects.edit', compact('project'));
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

public function show(TaskFilterRequest $request, Project $project)
    {
        $filters = $request->validated();

        $search = $filters['search'] ?? null;
        $assigneeId = $filters['assignee_id'] ?? null;
        $dueFrom = $filters['due_from'] ?? null;
        $dueTo = $filters['due_to'] ?? null;
        $columnId = $filters['column_id'] ?? null;
        $sortDueDate = $filters['sort_due_date'] ?? null;

        $project->load([
            'boardColumns' => function ($columnQuery) use (
                $search,
                $assigneeId,
                $dueFrom,
                $dueTo,
                $columnId,
                $sortDueDate
            ) {
                if ($columnId) {
                    $columnQuery->where('id', $columnId);
                }

                $columnQuery->with([
                    'tasks' => function ($taskQuery) use (
                        $search,
                        $assigneeId,
                        $dueFrom,
                        $dueTo,
                        $sortDueDate
                    ) {
                        if ($search) {
                            $taskQuery->where(function ($query) use ($search) {
                                $query
                                    ->where('title', 'like', '%' . $search . '%')
                                    ->orWhere(
                                        'description',
                                        'like',
                                        '%' . $search . '%'
                                    );
                            });
                        }

                        if ($assigneeId) {
                            $taskQuery->where('assignee_id', $assigneeId);
                        }

                        if ($dueFrom) {
                            $taskQuery->whereDate('due_date', '>=', $dueFrom);
                        }

                        if ($dueTo) {
                            $taskQuery->whereDate('due_date', '<=', $dueTo);
                        }

                        if ($sortDueDate === 'asc') {
                            $taskQuery->orderByRaw(
                                'due_date IS NULL, due_date ASC'
                            );
                        } elseif ($sortDueDate === 'desc') {
                            $taskQuery->orderByRaw(
                                'due_date IS NULL, due_date DESC'
                            );
                        }

                        $taskQuery
                            ->orderBy('position')
                            ->with(['comments.user']);
                    },
                ]);
            },
        ]);

        $users = $project->company->users;

        return view('projects.show', compact(
            'project',
            'users',
            'search',
            'assigneeId',
            'dueFrom',
            'dueTo',
            'columnId',
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
        $this->authorize('update', $project);
        
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