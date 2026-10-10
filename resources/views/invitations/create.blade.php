
<x-layouts.app title="Create Teammate Account">
    <div class="p-6 max-w-3xl mx-auto">
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
            <div class="p-6">
                <h1 class="text-2xl font-bold mb-2">
                    Create Teammate Account
                </h1>

                <p class="text-gray-600 mb-6">
                    Create an account for someone in your company.
                    They can log in immediately using the credentials you provide.
                </p>

                @if (session('success'))
                    <div class="mb-4 p-4 bg-green-100 text-green-700 rounded">
                        {{ session('success') }}
                    </div>
                @endif

                @if ($errors->any())
                    <div class="mb-4 p-4 bg-red-100 text-red-700 rounded">
                        <p class="font-medium mb-2">
                            Please fix the following errors:
                        </p>

                        <ul class="list-disc list-inside">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <form
                    method="POST"
                    action="{{ route('invitations.store') }}"
                    class="space-y-6"
                >
                    @csrf

                    <div>
                        <label for="name" class="block text-sm font-medium text-gray-700">
                            Full Name
                        </label>

                        <input
                            id="name"
                            type="text"
                            name="name"
                            value="{{ old('name') }}"
                            required
                            autocomplete="name"
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm"
                        >
                    </div>

                    <div>
                        <label for="email" class="block text-sm font-medium text-gray-700">
                            Email
                        </label>

                        <input
                            id="email"
                            type="email"
                            name="email"
                            value="{{ old('email') }}"
                            required
                            autocomplete="email"
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm"
                        >
                    </div>

                    <div>
                        <label for="role" class="block text-sm font-medium text-gray-700">
                            Role
                        </label>

                        <select
                            id="role"
                            name="role"
                            required
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm"
                        >
                            @foreach ($allowedRoles as $role)
                                <option
                                    value="{{ $role }}"
                                    @selected(old('role', 'member') === $role)
                                >
                                    {{ ucfirst($role) }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label for="password" class="block text-sm font-medium text-gray-700">
                            Password
                        </label>

                        <input
                            id="password"
                            type="password"
                            name="password"
                            required
                            minlength="8"
                            autocomplete="new-password"
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm"
                        >

                        <p class="mt-1 text-sm text-gray-500">
                            Must be at least 8 characters long.
                        </p>
                    </div>

                    <div>
                        <label for="password_confirmation" class="block text-sm font-medium text-gray-700">
                            Confirm Password
                        </label>

                        <input
                            id="password_confirmation"
                            type="password"
                            name="password_confirmation"
                            required
                            minlength="8"
                            autocomplete="new-password"
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm"
                        >
                    </div>

                    <div>
                        <button
                            type="submit"
                            class="px-4 py-2 bg-black text-white rounded hover:bg-gray-800"
                        >
                            Create Account
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-layouts.app>
