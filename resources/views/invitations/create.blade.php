<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Invite Teammate
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">

                @if (session('success'))
                    <div class="mb-4 text-green-600">
                        {{ session('success') }}
                    </div>
                @endif

                <form method="POST" action="{{ route('invitations.store') }}">
                    @csrf

                    <div>
                        <label for="email">Email</label>

                        <input
                            id="email"
                            type="email"
                            name="email"
                            value="{{ old('email') }}"
                            required
                        >

                        @error('email')
                            <p>{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="role">Role</label>

                        <select id="role" name="role" required>
                            <option value="member">Member</option>
                            <option value="admin">Admin</option>
                        </select>

                        @error('role')
                            <p>{{ $message }}</p>
                        @enderror
                    </div>

                    <button type="submit">
                        Invite Teammate
                    </button>
                </form>

            </div>
        </div>
    </div>
</x-app-layout>