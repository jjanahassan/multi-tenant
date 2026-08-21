<x-layouts.app :title="__('Projects')">
    <div class="p-6">

        <div class="flex items-center justify-between mb-6">
            <h1 class="text-2xl font-bold">Projects</h1>

            <a
                href="{{ route('projects.create') }}"
                class="px-4 py-2 bg-black text-white rounded"
            >
                Create Project
            </a>
        </div>

        @if (session('success'))
            <div class="mb-4 p-4 bg-green-100 rounded">
                {{ session('success') }}
            </div>
        @endif

        @forelse ($projects as $project)
            <div class="border rounded p-4 mb-3">
                <h2 class="font-semibold text-lg">
                    <a href="{{ route('projects.show', $project) }}">
                        {{ $project->name }}
                    </a>
                </h2>

                @if ($project->description)
                    <p class="text-gray-600">
                        {{ $project->description }}
                    </p>
                @endif
            </div>
        @empty
            <p>No projects yet.</p>
        @endforelse

    </div>
</x-layouts.app>