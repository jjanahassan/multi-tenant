<x-layouts.app :title="$project->name">
    <div class="p-6">

        @if (session('success'))
            <div class="mb-4 p-4 bg-green-100 rounded">
                {{ session('success') }}
            </div>
        @endif

        <div class="flex items-center justify-between mb-6">

            <div>
                <h1 class="text-2xl font-bold">
                    {{ $project->name }}
                </h1>

                @if ($project->description)
                    <p class="text-gray-600">
                        {{ $project->description }}
                    </p>
                @endif
            </div>

            <div class="flex gap-2">

                <a
                    href="{{ route('projects.edit', $project) }}"
                    class="px-4 py-2 border rounded"
                >
                    Edit
                </a>

                <form
                    method="POST"
                    action="{{ route('projects.destroy', $project) }}"
                >
                    @csrf
                    @method('DELETE')

                    <button
                        type="submit"
                        class="px-4 py-2 bg-red-600 text-white rounded"
                    >
                        Delete
                    </button>
                </form>

            </div>

        </div>

        <h2 class="text-xl font-semibold">
            Board
        </h2>

        <p class="text-gray-500">
            No columns yet.
        </p>

    </div>
</x-layouts.app>