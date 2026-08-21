<x-layouts.app :title="__('Create Project')">
    <div class="p-6 max-w-xl">

        <h1 class="text-2xl font-bold mb-6">
            Create Project
        </h1>

        <form method="POST" action="{{ route('projects.store') }}">
            @csrf

            <div class="mb-4">
                <label class="block mb-2">Project Name</label>

                <input
                    type="text"
                    name="name"
                    value="{{ old('name') }}"
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
                >{{ old('description') }}</textarea>

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
                Create Project
            </button>

        </form>

    </div>
</x-layouts.app>