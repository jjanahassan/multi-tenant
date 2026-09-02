<x-layouts.app :title="$project->name">

    <div class="p-6">

        {{-- Success message --}}
        @if (session('success'))
            <div class="mb-4 p-4 bg-green-100 text-green-800 rounded">
                {{ session('success') }}
            </div>
        @endif

        {{-- Project Header --}}
        <div class="flex items-start justify-between mb-8">

            <div>
                <h1 class="text-2xl font-bold">
                    {{ $project->name }}
                </h1>

                @if ($project->description)
                    <p class="text-gray-600 mt-1">
                        {{ $project->description }}
                    </p>
                @endif
            </div>

            <div class="flex items-center gap-2">

                @can('update', $project)
                    <a
                        href="{{ route('projects.edit', $project) }}"
                        class="inline-flex items-center px-4 py-2 border rounded"
                    >
                        Edit Project
                    </a>
                @endcan

                @can('delete', $project)
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
                            Delete Project
                        </button>
                    </form>
                @endcan

            </div>

        </div>


        {{-- ========================= --}}
        {{-- BOARD COLUMNS --}}
        {{-- ========================= --}}

        <div class="mb-6">

            <div class="flex items-center justify-between mb-4">

                <h2 class="text-xl font-semibold">
                    Board
                </h2>

                @can('update', $project)
                    <form
                        method="POST"
                        action="{{ route('projects.columns.store', $project) }}"
                        class="flex gap-2"
                    >
                        @csrf

                        <input
                            type="text"
                            name="name"
                            placeholder="New column name"
                            value="{{ old('name') }}"
                            class="border rounded px-3 py-2"
                        >

                        <button
                            type="submit"
                            class="px-4 py-2 bg-black text-white rounded"
                        >
                            Add Column
                        </button>
                    </form>
                @endcan

            </div>

            @error('name')
                <p class="text-red-500 text-sm mb-4">
                    {{ $message }}
                </p>
            @enderror


            {{-- ========================= --}}
            {{-- TASK FILTERS --}}
            {{-- ========================= --}}
            <div class="mb-6 p-5 bg-white border rounded-lg shadow-sm">
                <div class="flex items-center justify-between mb-4">
                    <div>
                        <h2 class="text-lg font-semibold">
                            Task Filters
                        </h2>
                        <p class="text-sm text-gray-500">
                            Filter and sort the tasks on this board.
                        </p>
                    </div>

                    <a
                        href="{{ route('projects.show', $project) }}"
                        class="text-sm text-gray-600 hover:text-black underline"
                    >
                        Clear filters
                    </a>
                </div>

                <form
                    method="GET"
                    action="{{ route('projects.show', $project) }}"
                    class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 items-end"
                >

                    {{-- Assignee --}}
                    <div>
                        <label
                            for="assignee_id"
                            class="block text-sm font-medium text-gray-700 mb-1"
                        >
                            Assignee
                        </label>

                        <select
                            id="assignee_id"
                            name="assignee_id"
                            class="w-full border border-gray-300 rounded-md px-3 py-2 bg-white"
                        >
                            <option value="">
                                All assignees
                            </option>

                            @foreach ($users as $user)
                                <option
                                    value="{{ $user->id }}"
                                    @selected(request('assignee_id') == $user->id)
                                >
                                    {{ $user->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    {{-- Due Date --}}
                    <div>
                        <label
                            for="due_date"
                            class="block text-sm font-medium text-gray-700 mb-1"
                        >
                            Due Date
                        </label>

                        <input
                            id="due_date"
                            type="date"
                            name="due_date"
                            value="{{ request('due_date') }}"
                            class="w-full border border-gray-300 rounded-md px-3 py-2"
                        >
                    </div>

                    {{-- Sort --}}
                    <div>
                        <label
                            for="sort"
                            class="block text-sm font-medium text-gray-700 mb-1"
                        >
                            Sort by Due Date
                        </label>

                        <select
                            id="sort"
                            name="sort_due_date"
                            class="w-full border border-gray-300 rounded-md px-3 py-2 bg-white"
                        >
                            <option
                                value=""
                                @selected(!request('sort'))
                            >
                                Default
                            </option>

                            <option
                                value="asc"
                                @selected(request('sort') === 'asc')
                            >
                                Earliest first
                            </option>

                            <option
                                value="desc"
                                @selected(request('sort') === 'desc')
                            >
                                Latest first
                            </option>
                        </select>
                    </div>

                    {{-- Apply --}}
                    <div>
                        <button
                            type="submit"
                            class="w-full px-4 py-2 bg-black text-white rounded-md hover:bg-gray-800 transition"
                        >
                            Apply Filters
                        </button>
                    </div>

                </form>
            </div>

            {{-- Kanban Board --}}
            <div class="flex gap-4 overflow-x-auto pb-4">

                @forelse ($project->boardColumns as $column)

                    <div class="border rounded-lg p-4 min-w-[280px] w-[280px] bg-gray-50"
                        data-column-id="{{ $column->id }}">

                        {{-- Column Header --}}
                        <div class="flex items-start justify-between gap-2 mb-4">

                            <div class="flex-1">

                                @can('update', $project)

                                    {{-- Rename --}}
                                    <form
                                        method="POST"
                                        action="{{ route(
                                            'projects.columns.update',
                                            [$project, $column]
                                        ) }}"
                                    >
                                        @csrf
                                        @method('PUT')

                                        <div class="flex gap-1">

                                            <input
                                                type="text"
                                                name="name"
                                                value="{{ $column->name }}"
                                                class="border rounded px-2 py-1 w-full text-sm"
                                            >

                                            <button
                                                type="submit"
                                                class="px-2 py-1 border rounded text-sm"
                                            >
                                                Rename
                                            </button>

                                        </div>
                                    </form>

                                @else

                                    <h3 class="font-semibold">
                                        {{ $column->name }}
                                    </h3>

                                @endcan

                            </div>

                        </div>


                        {{-- Column Controls --}}
                        @can('update', $project)

                            <div class="flex items-center justify-between mb-4">

                                <div class="flex gap-1">

                                    {{-- Move Left --}}
                                    <form
                                        method="POST"
                                        action="{{ route(
                                            'projects.columns.reorder',
                                            [
                                                'project' => $project,
                                                'boardColumn' => $column,
                                                'direction' => 'left',
                                            ]
                                        ) }}"
                                    >
                                        @csrf
                                        @method('PATCH')

                                        <button
                                            type="submit"
                                            class="px-2 py-1 border rounded"
                                            title="Move left"
                                        >
                                            ←
                                        </button>
                                    </form>

                                    {{-- Move Right --}}
                                    <form
                                        method="POST"
                                        action="{{ route(
                                            'projects.columns.reorder',
                                            [
                                                'project' => $project,
                                                'boardColumn' => $column,
                                                'direction' => 'right',
                                            ]
                                        ) }}"
                                    >
                                        @csrf
                                        @method('PATCH')

                                        <button
                                            type="submit"
                                            class="px-2 py-1 border rounded"
                                            title="Move right"
                                        >
                                            →
                                        </button>
                                    </form>

                                </div>


                                {{-- Delete Column --}}
                                <form
                                    method="POST"
                                    action="{{ route(
                                        'projects.columns.destroy',
                                        [$project, $column]
                                    ) }}"
                                >
                                    @csrf
                                    @method('DELETE')

                                    <button
                                        type="submit"
                                        class="text-red-600 text-sm"
                                    >
                                        Delete
                                    </button>
                                </form>

                            </div>

                        @endcan


                        {{-- ========================= --}}
                        {{-- TASKS --}}
                        {{-- ========================= --}}

                        <div
                            class="task-list space-y-3 min-h-[80px]"
                            data-column-id="{{ $column->id }}"
                        >

                            @forelse ($column->tasks as $task)

                                <div
                                    class="task-card border rounded-lg p-4 mb-3 bg-white shadow-sm cursor-move"
                                    draggable="true"
                                    data-task-id="{{ $task->id }}"
                                >

                                    <div class="flex justify-between items-start gap-2">

                                        <h3 class="font-semibold">
                                            {{ $task->title }}
                                        </h3>

                                        <span class="text-xs text-gray-500">
                                            #{{ $task->id }}
                                        </span>

                                    </div>

                                    @if ($task->description)
                                        <p class="text-sm text-gray-600 mt-2">
                                            {{ $task->description }}
                                        </p>
                                    @endif

                                    @if ($task->assignee)
                                        <p class="text-sm mt-3">
                                            <strong>Assigned to:</strong>
                                            {{ $task->assignee->name }}
                                        </p>
                                    @else
                                        <p class="text-sm text-gray-500 mt-3">
                                            Unassigned
                                        </p>
                                    @endif

                                    @if ($task->due_date)
                                        <p class="text-sm mt-1">
                                            <strong>Due:</strong>
                                            {{ $task->due_date->format('M d, Y') }}
                                        </p>
                                    @endif


                                </div>

                                {{-- Comments --}}
                                <div class="mt-4 pt-4 border-t">

                                    <h4 class="font-medium text-sm mb-3">
                                        Comments
                                    </h4>

                                    <div class="space-y-2 mb-4">
                                        @forelse ($task->comments->sortBy('created_at') as $comment)

                                            <div class="bg-gray-50 rounded p-3">

                                                <div class="flex items-start justify-between gap-2">

                                                    <div>
                                                        <p class="text-sm font-medium">
                                                            {{ $comment->user->name }}
                                                        </p>

                                                        <p class="text-xs text-gray-500">
                                                            {{ $comment->created_at->format('M d, Y H:i') }}
                                                        </p>

                                                        <p class="text-sm mt-2">
                                                            {{ $comment->body }}
                                                        </p>
                                                    </div>


                                                    @if ($comment->user_id === auth()->id())
                                                        <form
                                                            method="POST"
                                                            action="{{ route('comments.destroy', $comment) }}"
                                                        >
                                                            @csrf
                                                            @method('DELETE')

                                                            <button
                                                                type="submit"
                                                                class="text-xs text-red-600"
                                                            >
                                                                Delete
                                                            </button>
                                                        </form>
                                                    @endif

                                                </div>

                                                

                                            </div>

                                        @empty

                                            <p class="text-sm text-gray-500">
                                                No comments yet.
                                            </p>

                                        @endforelse
                                    </div>

                                    {{-- Add Comment --}}
                                    <form
                                        method="POST"
                                        action="{{ route('tasks.comments.store', $task) }}"
                                        class="space-y-2"
                                    >
                                        @csrf

                                        <textarea
                                            name="body"
                                            rows="2"
                                            placeholder="Write a comment..."
                                            class="border rounded px-3 py-2 w-full text-sm"
                                            required
                                        >{{ old('body') }}</textarea>

                                        @error('body')
                                            <p class="text-red-500 text-xs">
                                                {{ $message }}
                                            </p>
                                        @enderror

                                        <button
                                            type="submit"
                                            class="px-3 py-2 bg-black text-white rounded text-sm"
                                        >
                                            Add Comment
                                        </button>
                                    </form>

                                </div>
                            @empty

                                <p class="empty-column text-sm text-gray-500">
                                    No tasks yet.
                                </p>

                            @endforelse

                        </div>


                        {{-- ========================= --}}
                        {{-- ADD TASK --}}
                        {{-- ========================= --}}

                        @can('update', $project)

                            <div class="mt-4 pt-4 border-t">

                                <h4 class="font-medium mb-2">
                                    Add Task
                                </h4>

                                <form
                                    method="POST"
                                    action="{{ route(
                                        'projects.tasks.store',
                                        $project
                                    ) }}"
                                    class="space-y-2"
                                >

                                    @csrf

                                    {{-- Important: this task belongs to THIS column --}}
                                    <input
                                        type="hidden"
                                        name="board_column_id"
                                        value="{{ $column->id }}"
                                    >

                                    <input
                                        type="text"
                                        name="title"
                                        placeholder="Task title"
                                        value="{{ old('title') }}"
                                        class="border rounded px-3 py-2 w-full"
                                        required
                                    >

                                    <textarea
                                        name="description"
                                        placeholder="Description"
                                        class="border rounded px-3 py-2 w-full"
                                        rows="2"
                                    >{{ old('description') }}</textarea>


                                    {{-- Assignee --}}
                                    <select
                                        name="assignee_id"
                                        class="border rounded px-3 py-2 w-full"
                                    >
                                        <option value="">
                                            Unassigned
                                        </option>

                                        @foreach ($users as $user)
                                            <option
                                                value="{{ $user->id }}"
                                                @selected(old('assignee_id') == $user->id)
                                            >
                                                {{ $user->name }}
                                            </option>
                                        @endforeach

                                    </select>


                                    {{-- Due Date --}}
                                    <input
                                        type="date"
                                        name="due_date"
                                        value="{{ old('due_date') }}"
                                        class="border rounded px-3 py-2 w-full"
                                    >


                                    <button
                                        type="submit"
                                        class="w-full px-4 py-2 bg-black text-white rounded"
                                    >
                                        Add Task
                                    </button>

                                </form>

                            </div>

                        @endcan

                    </div>

                @empty

                    <p class="text-gray-500">
                        No board columns yet.
                    </p>

                @endforelse

            </div>

        </div>

    </div>

    <script>
    document.addEventListener('DOMContentLoaded', () => {

        let draggedTask = null;
        let sourceList = null;

        const taskCards = document.querySelectorAll('.task-card');
        const taskLists = document.querySelectorAll('.task-list');

        /*
        |--------------------------------------------------------------------------
        | Drag Start
        |--------------------------------------------------------------------------
        */

        taskCards.forEach(card => {

            card.addEventListener('dragstart', () => {
                draggedTask = card;
                sourceList = card.parentElement;

                card.classList.add('opacity-50');
            });

            card.addEventListener('dragend', () => {
                card.classList.remove('opacity-50');

                draggedTask = null;
                sourceList = null;
            });

        });


        /*
        |--------------------------------------------------------------------------
        | Allow Drop
        |--------------------------------------------------------------------------
        */

        taskLists.forEach(list => {

            list.addEventListener('dragover', event => {
                event.preventDefault();
            });


            /*
            |--------------------------------------------------------------------------
            | Drop
            |--------------------------------------------------------------------------
            */

            list.addEventListener('drop', async event => {

                event.preventDefault();

                if (!draggedTask || !sourceList) {
                    return;
                }

                /*
                |--------------------------------------------------------------------------
                | Don't move if dropped in the same column
                |--------------------------------------------------------------------------
                */

                if (sourceList === list) {
                    return;
                }


                const taskId = draggedTask.dataset.taskId;
                const columnId = list.dataset.columnId;


                /*
                |--------------------------------------------------------------------------
                | Remove "No tasks yet."
                |--------------------------------------------------------------------------
                */

                const emptyMessage = list.querySelector('.empty-column');

                if (emptyMessage) {
                    emptyMessage.remove();
                }


                /*
                |--------------------------------------------------------------------------
                | Move task visually
                |--------------------------------------------------------------------------
                */

                list.appendChild(draggedTask);


                /*
                |--------------------------------------------------------------------------
                | Show empty message in source column
                |--------------------------------------------------------------------------
                */

                if (!sourceList.querySelector('.task-card')) {

                    const message = document.createElement('p');

                    message.className =
                        'empty-column text-sm text-gray-500';

                    message.textContent = 'No tasks yet.';

                    sourceList.appendChild(message);
                }


                /*
                |--------------------------------------------------------------------------
                | Calculate task position
                |--------------------------------------------------------------------------
                |
                | Because the task is appended to the destination column,
                | its position is the number of task cards already in
                | that column minus 1.
                |
                */

                const tasks = list.querySelectorAll('.task-card');

                const position = tasks.length - 1;


                /*
                |--------------------------------------------------------------------------
                | Send move request to Laravel
                |--------------------------------------------------------------------------
                */

                try {

                    const response = await fetch(
                        "{{ route('projects.tasks.move', [$project, '__TASK_ID__']) }}"
                            .replace('__TASK_ID__', taskId),
                        {
                            method: 'PATCH',

                            headers: {
                                'Content-Type': 'application/json',
                                'Accept': 'application/json',
                                'X-CSRF-TOKEN':
                                    document
                                        .querySelector('meta[name="csrf-token"]')
                                        .getAttribute('content')
                            },

                            body: JSON.stringify({
                                board_column_id: columnId,
                                position: position
                            })
                        }
                    );


                    /*
                    |--------------------------------------------------------------------------
                    | Handle failed request
                    |--------------------------------------------------------------------------
                    */

                    if (!response.ok) {
                        throw new Error('Failed to move task.');
                    }

                } catch (error) {

                    console.error(error);

                    alert('The task could not be moved.');

                    /*
                    |--------------------------------------------------------------------------
                    | Reload so UI matches database
                    |--------------------------------------------------------------------------
                    */

                    window.location.reload();
                }

            });

        });

    });
    </script>

</x-layouts.app>