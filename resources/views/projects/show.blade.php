
<x-layouts.app :title="$project->name">
    <div class="min-h-screen bg-slate-50">
        <div class="mx-auto w-full max-w-[1600px] px-4 py-6 sm:px-6 lg:px-8">

            {{-- Flash message --}}
            @if (session('success'))
                <div class="mb-6 rounded-xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm font-medium text-emerald-800">
                    {{ session('success') }}
                </div>
            @endif

            {{-- Validation errors --}}
            @if ($errors->any())
                <div class="mb-6 rounded-xl border border-red-200 bg-red-50 p-4 text-sm text-red-700">
                    <p class="mb-2 font-semibold">Please check the following:</p>
                    <ul class="list-inside list-disc space-y-1">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            {{-- Project header --}}
            <header class="mb-8 flex flex-col gap-5 border-b border-slate-200 pb-6 lg:flex-row lg:items-center lg:justify-between">
                <div class="min-w-0">
                    <a href="{{ route('projects.index') }}"
                       class="mb-3 inline-flex items-center gap-2 text-sm font-medium text-slate-500 no-underline transition hover:text-indigo-600">
                        <span aria-hidden="true">&larr;</span>
                        All projects
                    </a>

                    <div class="flex flex-wrap items-center gap-3">
                        <div class="flex h-12 w-12 shrink-0 items-center justify-center rounded-2xl bg-indigo-100 text-xl font-bold text-indigo-700">
                            {{ strtoupper(substr($project->name, 0, 1)) }}
                        </div>

                        <div class="min-w-0">
                            <h1 class="break-words text-2xl font-bold tracking-tight text-slate-900 sm:text-3xl">
                                {{ $project->name }}
                            </h1>

                            @if ($project->description)
                                <p class="mt-1 max-w-3xl text-sm leading-6 text-slate-500">
                                    {{ $project->description }}
                                </p>
                            @else
                                <p class="mt-1 text-sm text-slate-500">
                                    Organize tasks and collaborate with your team.
                                </p>
                            @endif
                        </div>
                    </div>
                </div>

                <div class="flex flex-wrap items-center gap-2">
                    @can('update', $project)
                        <a href="{{ route('projects.edit', $project) }}"
                           class="inline-flex items-center justify-center rounded-lg border border-slate-300 bg-white px-4 py-2.5 text-sm font-semibold text-slate-700 no-underline transition hover:bg-slate-100">
                            Edit project
                        </a>
                    @endcan

                    @can('delete', $project)
                        <form method="POST"
                              action="{{ route('projects.destroy', $project) }}"
                              class="!m-0 !max-w-none !bg-transparent !p-0 !shadow-none"
                              onsubmit="return confirm('Delete this project? This action may remove its related data.');">
                            @csrf
                            @method('DELETE')

                            <button type="submit"
                                    class="inline-flex items-center justify-center rounded-lg bg-red-50 px-4 py-2.5 text-sm font-semibold text-red-700 transition hover:bg-red-100">
                                Delete project
                            </button>
                        </form>
                    @endcan
                </div>
            </header>

            {{-- Board section heading --}}
            <section>
                <div class="mb-5 flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
                    <div>
                        <h2 class="text-xl font-bold text-slate-900">Project board</h2>
                        <p class="mt-1 text-sm text-slate-500">
                            Track work from start to completion.
                        </p>
                    </div>

                    @can('update', $project)
                        <form method="POST"
                              action="{{ route('projects.columns.store', $project) }}"
                              class="!m-0 !flex !w-full !max-w-none !flex-col !gap-2 !bg-transparent !p-0 !shadow-none sm:!w-auto sm:!flex-row">
                            @csrf

                            <input type="text"
                                   name="name"
                                   value="{{ old('name') }}"
                                   placeholder="New column name"
                                   aria-label="New column name"
                                   class="!mb-0 !mt-0 !w-full !rounded-lg !border !border-slate-300 !bg-white !px-3 !py-2.5 !text-sm !text-slate-900 sm:!w-52"
                                   required>

                            <button type="submit"
                                    class="!w-full !rounded-lg !bg-indigo-600 !px-4 !py-2.5 !text-sm !font-semibold !text-white hover:!bg-indigo-700 sm:!w-auto">
                                + Add column
                            </button>
                        </form>
                    @endcan
                </div>

                {{-- Filters --}}
                <form method="GET"
                      action="{{ route('projects.show', $project) }}"
                      class="!mb-6 !mt-0 !w-full !max-w-none !rounded-2xl !border !border-slate-200 !bg-white !p-4 !shadow-sm sm:!p-5">
                    <div class="mb-4 flex flex-wrap items-center justify-between gap-2">
                        <h3 class="text-sm font-semibold text-slate-800">Filter tasks</h3>

                        <a href="{{ route('projects.show', $project) }}"
                           class="text-sm font-medium text-indigo-600 no-underline hover:text-indigo-800">
                            Clear filters
                        </a>
                    </div>

                    <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-6">
                        <div>
                            <label for="search" class="mb-1.5 block text-xs font-semibold text-slate-600">Search</label>
                            <input type="text"
                                   id="search"
                                   name="search"
                                   value="{{ $search }}"
                                   placeholder="Search tasks..."
                                   class="!mb-0 !mt-0 !w-full !rounded-lg !border !border-slate-300 !px-3 !py-2.5 !text-sm">
                        </div>

                        <div>
                            <label for="assignee_id" class="mb-1.5 block text-xs font-semibold text-slate-600">Assignee</label>
                            <select id="assignee_id"
                                    name="assignee_id"
                                    class="!mb-0 !mt-0 !w-full !rounded-lg !border !border-slate-300 !px-3 !py-2.5 !text-sm">
                                <option value="">All assignees</option>
                                @foreach ($users as $user)
                                    <option value="{{ $user->id }}"
                                        @selected((string) $assigneeId === (string) $user->id)>
                                        {{ $user->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div>
                            <label for="due_from" class="mb-1.5 block text-xs font-semibold text-slate-600">Due from</label>
                            <input type="date"
                                   id="due_from"
                                   name="due_from"
                                   value="{{ $dueFrom }}"
                                   class="!mb-0 !mt-0 !w-full !rounded-lg !border !border-slate-300 !px-3 !py-2.5 !text-sm">
                        </div>

                        <div>
                            <label for="due_to" class="mb-1.5 block text-xs font-semibold text-slate-600">Due to</label>
                            <input type="date"
                                   id="due_to"
                                   name="due_to"
                                   value="{{ $dueTo }}"
                                   class="!mb-0 !mt-0 !w-full !rounded-lg !border !border-slate-300 !px-3 !py-2.5 !text-sm">
                        </div>

                        <div>
                            <label for="column_id" class="mb-1.5 block text-xs font-semibold text-slate-600">Column</label>
                            <select id="column_id"
                                    name="column_id"
                                    class="!mb-0 !mt-0 !w-full !rounded-lg !border !border-slate-300 !px-3 !py-2.5 !text-sm">
                                <option value="">All columns</option>
                                @foreach ($project->boardColumns as $column)
                                    <option value="{{ $column->id }}"
                                        @selected((string) $columnId === (string) $column->id)>
                                        {{ $column->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div>
                            <label for="sort_due_date" class="mb-1.5 block text-xs font-semibold text-slate-600">Sort by due date</label>
                            <select id="sort_due_date"
                                    name="sort_due_date"
                                    class="!mb-0 !mt-0 !w-full !rounded-lg !border !border-slate-300 !px-3 !py-2.5 !text-sm">
                                <option value="">Default order</option>
                                <option value="asc" @selected($sortDueDate === 'asc')>Earliest first</option>
                                <option value="desc" @selected($sortDueDate === 'desc')>Latest first</option>
                            </select>
                        </div>
                    </div>

                    <div class="mt-4 flex justify-end">
                        <button type="submit"
                                class="!w-full !rounded-lg !bg-indigo-600 !px-5 !py-2.5 !text-sm !font-semibold !text-white hover:!bg-indigo-700 sm:!w-auto">
                            Apply filters
                        </button>
                    </div>
                </form>

                {{-- Kanban columns --}}
                <div class="flex items-start gap-5 overflow-x-auto pb-6"
                     style="min-height: 350px;">

                    @forelse ($project->boardColumns as $column)
                        <section class="w-[320px] min-w-[320px] shrink-0 overflow-hidden rounded-2xl border border-slate-200 bg-slate-100/80"
                                 data-column-id="{{ $column->id }}">

                            {{-- Column header --}}
                            <div class="border-b border-slate-200 bg-white p-4">
                                @can('update', $project)
                                    <form method="POST"
                                          action="{{ route('projects.columns.update', [$project, $column]) }}"
                                          class="!m-0 !flex !max-w-none !gap-2 !bg-transparent !p-0 !shadow-none">
                                        @csrf
                                        @method('PUT')

                                        <input type="text"
                                               name="name"
                                               value="{{ $column->name }}"
                                               aria-label="Column name"
                                               class="!mb-0 !mt-0 !min-w-0 !flex-1 !rounded-lg !border !border-slate-200 !px-2 !py-2 !text-sm !font-semibold"
                                               required>

                                        <button type="submit"
                                                title="Rename column"
                                                class="!rounded-lg !bg-slate-100 !px-3 !py-2 !text-xs !font-semibold !text-slate-700 hover:!bg-slate-200">
                                            Save
                                        </button>
                                    </form>
                                @else
                                    <h3 class="break-words text-sm font-bold text-slate-800">
                                        {{ $column->name }}
                                    </h3>
                                @endcan

                                <div class="mt-3 flex items-center justify-between">
                                    <span class="rounded-full bg-slate-100 px-2.5 py-1 text-xs font-semibold text-slate-600">
                                        {{ $column->tasks->count() }} {{ \Illuminate\Support\Str::plural('task', $column->tasks->count()) }}
                                    </span>

                                    @can('update', $project)
                                        <div class="flex items-center gap-1">
                                            {{-- Move left --}}
                                            <form method="POST"
                                                  action="{{ route('projects.columns.reorder', ['project' => $project, 'boardColumn' => $column, 'direction' => 'left']) }}"
                                                  class="!m-0 !max-w-none !bg-transparent !p-0 !shadow-none">
                                                @csrf
                                                @method('PATCH')
                                                <button type="submit"
                                                        aria-label="Move column left"
                                                        title="Move left"
                                                        class="!rounded-md !bg-slate-100 !px-2.5 !py-1.5 !text-sm !text-slate-600 hover:!bg-indigo-50 hover:!text-indigo-700">
                                                    &larr;
                                                </button>
                                            </form>

                                            {{-- Move right --}}
                                            <form method="POST"
                                                  action="{{ route('projects.columns.reorder', ['project' => $project, 'boardColumn' => $column, 'direction' => 'right']) }}"
                                                  class="!m-0 !max-w-none !bg-transparent !p-0 !shadow-none">
                                                @csrf
                                                @method('PATCH')
                                                <button type="submit"
                                                        aria-label="Move column right"
                                                        title="Move right"
                                                        class="!rounded-md !bg-slate-100 !px-2.5 !py-1.5 !text-sm !text-slate-600 hover:!bg-indigo-50 hover:!text-indigo-700">
                                                    &rarr;
                                                </button>
                                            </form>

                                            {{-- Delete column --}}
                                            <form method="POST"
                                                  action="{{ route('projects.columns.destroy', [$project, $column]) }}"
                                                  class="!m-0 !max-w-none !bg-transparent !p-0 !shadow-none"
                                                  onsubmit="return confirm('Delete this column?');">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit"
                                                        class="!rounded-md !bg-red-50 !px-2.5 !py-1.5 !text-xs !font-semibold !text-red-600 hover:!bg-red-100">
                                                    Delete
                                                </button>
                                            </form>
                                        </div>
                                    @endcan
                                </div>
                            </div>

                            {{-- Task list --}}
                            <div class="task-list min-h-[100px] space-y-3 p-3"
                                 data-column-id="{{ $column->id }}">

                                @forelse ($column->tasks as $task)
                                    {{-- Whole task card is draggable, including comments --}}
                                    <article class="task-card rounded-xl border border-slate-200 bg-white p-4 shadow-sm transition hover:border-indigo-200 hover:shadow-md"
                                             draggable="true"
                                             data-task-id="{{ $task->id }}">

                                        <div class="mb-3 flex items-start justify-between gap-3">
                                            <h4 class="min-w-0 break-words text-sm font-semibold leading-6 text-slate-900">
                                                {{ $task->title }}
                                            </h4>
                                            <span class="shrink-0 rounded-md bg-slate-100 px-2 py-1 text-[11px] font-medium text-slate-500">
                                                #{{ $task->id }}
                                            </span>
                                        </div>

                                        @if ($task->description)
                                            <p class="mb-4 whitespace-pre-line break-words text-sm leading-6 text-slate-600">{{ $task->description }}</p>
                                        @endif

                                        <div class="space-y-2 border-t border-slate-100 pt-3">
                                            <div class="flex items-start gap-2 text-xs text-slate-600">
                                                <span class="font-semibold text-slate-500">Assignee</span>
                                                <span class="ml-auto text-right font-medium text-slate-700">
                                                    {{ $task->assignee?->name ?? 'Unassigned' }}
                                                </span>
                                            </div>

                                            @if ($task->due_date)
                                                <div class="flex items-center justify-between gap-2 text-xs">
                                                    <span class="font-semibold text-slate-500">Due date</span>
                                                    <span class="font-medium text-slate-700">
                                                        {{ $task->due_date->format('M d, Y') }}
                                                    </span>
                                                </div>
                                            @endif
                                        </div>

                                        {{-- Comments belong to this task card --}}
                                        <div class="mt-4 border-t border-slate-100 pt-4">
                                            <h5 class="mb-3 flex items-center gap-2 text-xs font-bold uppercase tracking-wide text-slate-500">
                                                Comments
                                                <span class="rounded-full bg-slate-100 px-2 py-0.5 text-[10px]">
                                                    {{ $task->comments->count() }}
                                                </span>
                                            </h5>

                                            <div class="mb-4 space-y-3">
                                                @forelse ($task->comments->sortBy('created_at') as $comment)
                                                    <div class="rounded-lg bg-slate-50 p-3">
                                                        <div class="flex items-start justify-between gap-2">
                                                            <div class="min-w-0">
                                                                <p class="break-words text-xs font-semibold text-slate-800">
                                                                    {{ $comment->user->name }}
                                                                </p>
                                                                <p class="mt-0.5 text-[11px] text-slate-400">
                                                                    {{ $comment->created_at->format('M d, Y H:i') }}
                                                                </p>
                                                            </div>

                                                            @if ($comment->user_id === auth()->id())
                                                                <form method="POST"
                                                                      action="{{ route('comments.destroy', $comment) }}"
                                                                      class="!m-0 !max-w-none !bg-transparent !p-0 !shadow-none"
                                                                      onsubmit="return confirm('Delete this comment?');">
                                                                    @csrf
                                                                    @method('DELETE')
                                                                    <button type="submit"
                                                                            class="!bg-transparent !p-0 !text-xs !font-medium !text-red-600 hover:!text-red-800">
                                                                        Delete
                                                                    </button>
                                                                </form>
                                                            @endif
                                                        </div>

                                                        <p class="mt-2 whitespace-pre-line break-words text-sm leading-5 text-slate-700">{{ $comment->body }}</p>
                                                    </div>
                                                @empty
                                                    <p class="text-xs text-slate-400">No comments yet.</p>
                                                @endforelse
                                            </div>

                                            {{-- Add comment --}}
                                            <form method="POST"
                                                  action="{{ route('tasks.comments.store', $task) }}"
                                                  class="!m-0 !max-w-none !space-y-2 !rounded-none !border-0 !bg-transparent !p-0 !shadow-none">
                                                @csrf

                                                <textarea name="body"
                                                          rows="2"
                                                          placeholder="Write a comment..."
                                                          aria-label="Write a comment"
                                                          class="!mb-0 !mt-0 !w-full !resize-y !rounded-lg !border !border-slate-200 !bg-white !px-3 !py-2 !text-sm"
                                                          required>{{ old('body') }}</textarea>

                                                <div class="flex justify-end">
                                                    <button type="submit"
                                                            class="!rounded-lg !bg-indigo-50 !px-3 !py-2 !text-xs !font-semibold !text-indigo-700 hover:!bg-indigo-100">
                                                        Add comment
                                                    </button>
                                                </div>
                                            </form>
                                        </div>
                                    </article>
                                @empty
                                    <p class="empty-column rounded-xl border border-dashed border-slate-300 bg-white/70 px-3 py-8 text-center text-xs text-slate-400">
                                        No tasks in this column yet.
                                    </p>
                                @endforelse
                            </div>

                            {{-- Add task --}}
                            @can('update', $project)
                                <div class="border-t border-slate-200 p-3">
                                    <details class="group">
                                        <summary class="flex cursor-pointer list-none items-center justify-center gap-2 rounded-lg border border-dashed border-slate-300 px-3 py-2.5 text-sm font-semibold text-slate-600 transition hover:border-indigo-300 hover:bg-white hover:text-indigo-700">
                                            <span class="text-lg leading-none">+</span>
                                            Add a task
                                        </summary>

                                        <form method="POST"
                                              action="{{ route('projects.tasks.store', $project) }}"
                                              class="!mb-0 !mt-3 !max-w-none !space-y-3 !rounded-xl !border !border-slate-200 !bg-white !p-3 !shadow-sm">
                                            @csrf

                                            <input type="hidden"
                                                   name="board_column_id"
                                                   value="{{ $column->id }}">

                                            <div>
                                                <label for="title-{{ $column->id }}" class="mb-1 block text-xs font-semibold text-slate-600">
                                                    Task title
                                                </label>
                                                <input id="title-{{ $column->id }}"
                                                       type="text"
                                                       name="title"
                                                       value="{{ old('title') }}"
                                                       placeholder="What needs to be done?"
                                                       class="!mb-0 !mt-0 !w-full !rounded-lg !border !border-slate-300 !px-3 !py-2 !text-sm"
                                                       required>
                                            </div>

                                            <div>
                                                <label for="description-{{ $column->id }}" class="mb-1 block text-xs font-semibold text-slate-600">
                                                    Description
                                                </label>
                                                <textarea id="description-{{ $column->id }}"
                                                          name="description"
                                                          rows="2"
                                                          placeholder="Add details (optional)"
                                                          class="!mb-0 !mt-0 !w-full !rounded-lg !border !border-slate-300 !px-3 !py-2 !text-sm">{{ old('description') }}</textarea>
                                            </div>

                                            <div>
                                                <label for="assignee-{{ $column->id }}" class="mb-1 block text-xs font-semibold text-slate-600">
                                                    Assign to
                                                </label>
                                                <select id="assignee-{{ $column->id }}"
                                                        name="assignee_id"
                                                        class="!mb-0 !mt-0 !w-full !rounded-lg !border !border-slate-300 !px-3 !py-2 !text-sm">
                                                    <option value="">Unassigned</option>
                                                    @foreach ($users as $user)
                                                        <option value="{{ $user->id }}"
                                                            @selected(old('assignee_id') == $user->id)>
                                                            {{ $user->name }}
                                                        </option>
                                                    @endforeach
                                                </select>
                                            </div>

                                            <div>
                                                <label for="due-date-{{ $column->id }}" class="mb-1 block text-xs font-semibold text-slate-600">
                                                    Due date
                                                </label>
                                                <input id="due-date-{{ $column->id }}"
                                                       type="date"
                                                       name="due_date"
                                                       value="{{ old('due_date') }}"
                                                       class="!mb-0 !mt-0 !w-full !rounded-lg !border !border-slate-300 !px-3 !py-2 !text-sm">
                                            </div>

                                            <button type="submit"
                                                    class="!w-full !rounded-lg !bg-indigo-600 !px-4 !py-2.5 !text-sm !font-semibold !text-white hover:!bg-indigo-700">
                                                Create task
                                            </button>
                                        </form>
                                    </details>
                                </div>
                            @endcan
                        </section>
                    @empty
                        <div class="w-full rounded-2xl border border-dashed border-slate-300 bg-white px-6 py-14 text-center">
                            <h3 class="text-base font-semibold text-slate-800">No columns yet</h3>
                            <p class="mt-2 text-sm text-slate-500">
                                Add a column above to start organizing your tasks.
                            </p>
                        </div>
                    @endforelse
                </div>
            </section>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', () => {
            let draggedTask = null;
            let sourceList = null;

            const taskCards = document.querySelectorAll('.task-card');
            const taskLists = document.querySelectorAll('.task-list');

            taskCards.forEach(card => {
                card.addEventListener('dragstart', event => {
                    draggedTask = card;
                    sourceList = card.closest('.task-list');

                    card.classList.add('opacity-50');

                    if (event.dataTransfer) {
                        event.dataTransfer.effectAllowed = 'move';
                        event.dataTransfer.setData('text/plain', card.dataset.taskId);
                    }
                });

                card.addEventListener('dragend', () => {
                    card.classList.remove('opacity-50');
                    draggedTask = null;
                    sourceList = null;
                });
            });

            taskLists.forEach(list => {
                list.addEventListener('dragover', event => {
                    event.preventDefault();

                    if (event.dataTransfer) {
                        event.dataTransfer.dropEffect = 'move';
                    }
                });

                list.addEventListener('drop', async event => {
                    event.preventDefault();

                    if (!draggedTask || !sourceList || sourceList === list) {
                        return;
                    }

                    const taskId = draggedTask.dataset.taskId;
                    const columnId = list.dataset.columnId;
                    const originalNextSibling = draggedTask.nextElementSibling;
                    const originalSourceList = sourceList;

                    const sourceEmptyMessage = sourceList.querySelector('.empty-column');
                    if (sourceEmptyMessage) {
                        sourceEmptyMessage.remove();
                    }

                    const destinationEmptyMessage = list.querySelector('.empty-column');
                    if (destinationEmptyMessage) {
                        destinationEmptyMessage.remove();
                    }

                    list.appendChild(draggedTask);

                    if (!sourceList.querySelector('.task-card')) {
                        const message = document.createElement('p');
                        message.className = 'empty-column rounded-xl border border-dashed border-slate-300 bg-white/70 px-3 py-8 text-center text-xs text-slate-400';
                        message.textContent = 'No tasks in this column yet.';
                        sourceList.appendChild(message);
                    }

                    const taskCardsInDestination = list.querySelectorAll('.task-card');
                    const position = taskCardsInDestination.length - 1;

                    try {
                        const response = await fetch(
                            @json(route('projects.tasks.move', [$project, '__TASK_ID__'])).replace('__TASK_ID__', taskId),
                            {
                                method: 'PATCH',
                                headers: {
                                    'Content-Type': 'application/json',
                                    'Accept': 'application/json',
                                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                                },
                                body: JSON.stringify({
                                    board_column_id: columnId,
                                    position: position
                                })
                            }
                        );

                        if (!response.ok) {
                            throw new Error(`Move failed with status ${response.status}`);
                        }
                    } catch (error) {
                        console.error('Unable to move task:', error);

                        alert('The task could not be moved. The board will be reloaded.');

                        // Restore the card position before reloading.
                        if (originalNextSibling && originalNextSibling.parentElement === originalSourceList) {
                            originalSourceList.insertBefore(draggedTask, originalNextSibling);
                        } else {
                            originalSourceList.appendChild(draggedTask);
                        }

                        window.location.reload();
                    }
                });
            });
        });
    </script>
</x-layouts.app>
