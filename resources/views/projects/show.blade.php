<x-layouts.app :title="$project->name">
    <div class="p-6">

        @if (session('success'))
            <div class="mb-4 p-4 bg-green-100 rounded">
                {{ session('success') }}
            </div>
        @endif

        <div class="flex items-start justify-between mb-6">

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

    <div class="flex items-center gap-2 shrink-0">

        <a
            href="{{ route('projects.edit', $project) }}"
            class="inline-flex items-center px-4 py-2 border rounded"
        >
            Edit
        </a>

        <form
            method="POST"
            action="{{ route('projects.destroy', $project) }}"
            class="inline-flex m-0 p-0"
        >
            @csrf
            @method('DELETE')

            <button
                type="submit"
                class="inline-flex items-center px-4 py-2 bg-red-600 text-white rounded"
            >
                Delete
            </button>
        </form>

    </div>

</div>

        <div class="mt-8">

    <h2 class="text-xl font-semibold mb-4">
        Board Columns
    </h2>

    <form
        method="POST"
        action="{{ route('projects.columns.store', $project) }}"
        class="flex gap-2 mb-6"
    >
        @csrf

        <input
            type="text"
            name="name"
            placeholder="Column name"
            value="{{ old('name') }}"
            class="border rounded p-2"
        >

        <button
            type="submit"
            class="px-4 py-2 bg-black text-white rounded"
        >
            Add Column
        </button>
    </form>

    @error('name')
        <p class="text-red-500 text-sm mb-4">
            {{ $message }}
        </p>
    @enderror

    <div class="flex gap-4">

        @forelse ($project->boardColumns as $column)

            <div class="border rounded p-4 min-w-56">

                {{-- Rename column --}}
                <form
                    method="POST"
                    action="{{ route('projects.columns.update', [$project, $column]) }}"
                >
                    @csrf
                    @method('PUT')

                    <input
                        type="text"
                        name="name"
                        value="{{ $column->name }}"
                        class="border rounded p-2 w-full"
                    >

                    <button
                        type="submit"
                        class="mt-2 px-3 py-1 border rounded"
                    >
                        Rename
                    </button>
                </form>

                {{-- Delete column --}}
                <form
                    method="POST"
                    action="{{ route('projects.columns.destroy', [$project, $column]) }}"
                    class="mt-2"
                >
                    @csrf
                    @method('DELETE')

                    <button
                        type="submit"
                        class="text-red-600"
                    >
                        Delete
                    </button>
                </form>

                {{-- Reorder buttons --}}
                <div class="flex gap-2 mt-3">

                    <form
                        method="POST"
                        action="{{ route('projects.columns.reorder', [
                            'project' => $project,
                            'boardColumn' => $column,
                            'direction' => 'left',
                        ]) }}"
                    >
                        @csrf
                        @method('PATCH')

                        <button
                            type="submit"
                            class="px-2 py-1 border rounded"
                        >
                            ←
                        </button>
                    </form>

                    <form
                        method="POST"
                        action="{{ route('projects.columns.reorder', [
                            'project' => $project,
                            'boardColumn' => $column,
                            'direction' => 'right',
                        ]) }}"
                    >
                        @csrf
                        @method('PATCH')

                        <button
                            type="submit"
                            class="px-2 py-1 border rounded"
                        >
                            →
                        </button>
                    </form>

                </div>

            </div>

        @empty

            <p class="text-gray-500">
                No board columns yet.
            </p>

        @endforelse

    </div>

</div>

    </div>
</x-layouts.app>