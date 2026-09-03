<x-layouts.app>
    <div class="max-w-4xl mx-auto py-8 px-4">
        <div class="flex items-center justify-between mb-6">
            <h1 class="text-2xl font-bold">
                Notifications
            </h1>
        </div>

        @if ($notifications->isEmpty())
            <div class="bg-white border rounded-lg p-6 text-gray-600">
                You don't have any notifications yet.
            </div>
        @else
            <div class="space-y-3">
                @foreach ($notifications as $notification)
                    <div
                        class="border rounded-lg p-4
                        {{ $notification->read_at ? 'bg-white' : 'bg-blue-50' }}"
                    >
                        <div class="flex items-start justify-between gap-4">
                            <div>
                                <p class="font-semibold">
                                    {{ $notification->data['message'] ?? 'You have a new notification.' }}
                                </p>

                                @if (!empty($notification->data['task_title']))
                                    <p class="text-sm text-gray-600 mt-1">
                                        Task:
                                        {{ $notification->data['task_title'] }}
                                    </p>
                                @endif

                                <p class="text-xs text-gray-500 mt-2">
                                    {{ $notification->created_at->diffForHumans() }}
                                </p>
                            </div>

                            @if (!$notification->read_at)
                                <form
                                    method="POST"
                                    action="{{ route('notifications.read', $notification->id) }}"
                                >
                                    @csrf

                                    <button
                                        type="submit"
                                        class="text-sm text-blue-600 hover:underline"
                                    >
                                        Mark as read
                                    </button>
                                </form>
                            @endif
                        </div>
                    </div>
                @endforeach
            </div>
        @endif
    </div>
</x-layouts.app>