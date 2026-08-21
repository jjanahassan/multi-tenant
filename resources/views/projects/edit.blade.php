<x-layouts.app :title="__('Edit Project')">
    <div class="p-6 max-w-xl">

        <h1 class="text-2xl font-bold mb-6">
            Edit Project
        </h1>

        <form
            method="POST"
            action="{{ route('projects.update', $project) }}"
        >
            @csrf
            @method('PUT')

            <div class="mb-4">
                <label class="block mb-2">Project Name</label>

                <input
                    type="text"
                    name="name"
                    value="{{ old('name', $project->name) }}"
                    class="w-full border rounded p-2"
                >

                @error('name')
                    <p class="text-red-500 text-sm mt-1">
                        {{ $message }}
                    </p>
                @enderror
            </div>

            <div class="mb-4">
                <label class="block mb-2">Description</label>

                <textarea
                    name="description"
                    class="w-full border rounded p-2"
                >{{ old('description', $project->description) }}</textarea>

                @error('description')
                    <p class="text-red-500 text-sm mt-1">
                        {{ $message }}
                    </p>
                @enderror
            </div>

            <button
                type="submit"
                class="px-4 py-2 bg-black text-white rounded"
            >
                Save Changes
            </button>

        </form>

    </div>
</x-layouts.app>