<x-layouts.app title="Manage Teammates">

    <div class="p-6 max-w-4xl mx-auto">

        <div class="flex items-center justify-between mb-6">

            <div>
                <h1 class="text-2xl font-bold">
                    Manage Teammates
                </h1>

                <p class="text-gray-600">
                    Manage users in {{ $company->name }}.
                </p>
            </div>

            @can('invite', $company)
                <a
                    href="{{ route('invitations.create') }}"
                    class="px-4 py-2 bg-black text-white rounded"
                >
                    Invite Teammate
                </a>
            @endcan

        </div>

        @if (session('success'))

            <div class="mb-4 p-4 bg-green-100 rounded">
                {{ session('success') }}
            </div>

        @endif


        <div class="bg-white border rounded">

            @foreach ($users as $user)

                <div class="flex items-center justify-between p-4 border-b last:border-b-0">

                    <div>

                        <div class="font-semibold">
                            {{ $user->name }}

                            @if ($user->id === $company->owner_id)

                                <span class="text-sm text-gray-500">
                                    (Owner)
                                </span>

                            @endif

                        </div>

                        <div class="text-sm text-gray-600">
                            {{ $user->email }}
                        </div>

                    </div>


                    <div class="flex items-center gap-4">

                        <span class="text-sm capitalize">
                            {{ $user->role }}
                        </span>


                        @if ($user->id !== $company->owner_id)

                            <form
                                method="POST"
                                action="{{ route('company.users.destroy', [$company, $user]) }}"
                            >

                                @csrf
                                @method('DELETE')

                                <button
                                    type="submit"
                                    class="text-red-600"
                                >
                                    Remove
                                </button>

                            </form>

                        @endif

                    </div>

                </div>

            @endforeach

        </div>

        @can('delete', $company)

    <div class="mt-8 border border-red-200 rounded p-6">

        <h2 class="text-xl font-bold text-red-600">
            Danger Zone
        </h2>

        <p class="text-gray-600 mt-2">
            Deleting this company will permanently remove the company and its associated data.
        </p>

        <form
            method="POST"
            action="{{ route('company.destroy', $company) }}"
            class="mt-4"
        >

            @csrf
            @method('DELETE')

            <button
                type="submit"
                class="px-4 py-2 bg-red-600 text-white rounded"
            >
                Delete Company
            </button>

        </form>

    </div>

@endcan
    </div>

</x-layouts.app>