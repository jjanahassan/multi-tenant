<x-layouts.app>
    <div class="flex flex-col gap-6 p-6">

        {{-- Project Header --}}
        <div class="flex items-center justify-between">
            <div>
                <flux:heading size="xl">
                    {{ $project->name }}
                </flux:heading>

                @if ($project->description)
                    <flux:text class="mt-1">
                        {{ $project->description }}
                    </flux:text>
                @endif
            </div>

            <a href="{{ route('projects.index') }}">
                <flux:button variant="ghost">
                    Back to Projects
                </flux:button>
            </a>
        </div>

        {{-- Kanban Board --}}
        <div class="grid grid-cols-1 gap-6 md:grid-cols-3">

            @foreach ($project->boardColumns as $column)

                <div class="rounded-xl bg-zinc-100 p-4">

                    {{-- Column Header --}}
                    <div class="mb-4 flex items-center justify-between">
                        <flux:heading size="lg">
                            {{ $column->name }}
                        </flux:heading>

                        <flux:text>
                            {{ $column->tasks->count() }}
                        </flux:text>
                    </div>

                    {{-- Tasks --}}
                    <div class="flex flex-col gap-3">

                        @forelse ($column->tasks->sortBy('position') as $task)

                            <div class="rounded-lg bg-white p-4 shadow-sm">

                                {{-- Task Title --}}
                                <flux:heading size="sm">
                                    {{ $task->title }}
                                </flux:heading>

                                {{-- Description --}}
                                @if ($task->description)
                                    <flux:text class="mt-2 text-sm">
                                        {{ $task->description }}
                                    </flux:text>
                                @endif

                                {{-- Task Details --}}
                                <div class="mt-4 flex flex-col gap-2">

                                    @if ($task->assignee)
                                        <flux:text class="text-sm">
                                            Assigned to:
                                            <strong>
                                                {{ $task->assignee->name }}
                                            </strong>
                                        </flux:text>
                                    @else
                                        <flux:text class="text-sm">
                                            Unassigned
                                        </flux:text>
                                    @endif

                                    @if ($task->due_date)
                                        <flux:text class="text-sm">
                                            Due:
                                            {{ $task->due_date->format('M d, Y') }}
                                        </flux:text>
                                    @endif

                                </div>

                            </div>

                        @empty

                            <div class="rounded-lg border border-dashed p-4 text-center">
                                <flux:text>
                                    No tasks yet.
                                </flux:text>
                            </div>

                        @endforelse

                    </div>

                </div>

            @endforeach

        </div>

    </div>
</x-layouts.app>