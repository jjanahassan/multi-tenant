
<x-layouts.app :title="__('Projects')">
    <div class="page-container">

        {{-- Page heading --}}
        <div class="mb-8 flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <p class="mb-2 text-sm font-medium text-indigo-600">
                    Workspace
                </p>

                <h1 class="page-title">Projects</h1>

                <p class="mt-2 text-sm text-slate-500">
                    Manage your projects and keep your team on track.
                </p>
            </div>

            @can('create', App\Models\Project::class)
                <a href="{{ route('projects.create') }}" class="btn-primary">
                    <span class="mr-2 text-lg leading-none">+</span>
                    Create project
                </a>
            @endcan
        </div>

        {{-- Success message --}}
        @if (session('success'))
            <div class="mb-6 rounded-lg border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-800">
                {{ session('success') }}
            </div>
        @endif

        {{-- Projects grid --}}
        @if ($projects->isNotEmpty())
            <div class="mb-4 flex items-center justify-between">
                <h2 class="text-sm font-semibold text-slate-700">
                    Your projects
                </h2>

                <span class="text-sm text-slate-500">
                    {{ $projects->count() }} {{ \Illuminate\Support\Str::plural('project', $projects->count()) }}
                </span>
            </div>

            <div class="grid grid-cols-1 gap-5 md:grid-cols-2 xl:grid-cols-3">
                @foreach ($projects as $project)
                    <a
                        href="{{ route('projects.show', $project) }}"
                        class="group block rounded-xl border border-slate-200 bg-white p-5 no-underline shadow-sm transition duration-200 hover:-translate-y-0.5 hover:border-indigo-200 hover:shadow-md"
                    >
                        <div class="mb-4 flex items-start justify-between gap-3">
                            <div class="flex h-11 w-11 items-center justify-center rounded-xl bg-indigo-50 text-lg font-bold text-indigo-700">
                                {{ strtoupper(substr($project->name, 0, 1)) }}
                            </div>

                            <span class="rounded-full bg-slate-100 px-2.5 py-1 text-xs font-medium text-slate-600">
                                Project
                            </span>
                        </div>

                        <h3 class="text-lg font-semibold text-slate-900 transition group-hover:text-indigo-700">
                            {{ $project->name }}
                        </h3>

                        <p class="mt-2 min-h-12 text-sm leading-6 text-slate-500">
                            {{ $project->description ?: 'No description added yet.' }}
                        </p>

                        <div class="mt-5 flex items-center justify-between border-t border-slate-100 pt-4">
                            <span class="text-sm font-medium text-indigo-600">
                                Open project
                            </span>

                            <span class="text-lg text-slate-400 transition group-hover:translate-x-1 group-hover:text-indigo-600">
                                &rarr;
                            </span>
                        </div>
                    </a>
                @endforeach
            </div>
        @else
            <div class="card px-6 py-16 text-center">
                <div class="mx-auto flex h-14 w-14 items-center justify-center rounded-2xl bg-indigo-50 text-2xl text-indigo-600">
                    +
                </div>

                <h2 class="mt-5 text-lg font-semibold text-slate-900">
                    No projects yet
                </h2>

                <p class="mx-auto mt-2 max-w-sm text-sm leading-6 text-slate-500">
                    Create your first project to organize tasks and collaborate with your team.
                </p>

                @can('create', App\Models\Project::class)
                    <a href="{{ route('projects.create') }}" class="btn-primary mt-6">
                        Create your first project
                    </a>
                @endcan
            </div>
        @endif

    </div>
</x-layouts.app>
