<x-layouts.app title="Invite Teammate">

    <div class="p-6 max-w-3xl mx-auto">

        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">

            <div class="p-6">

                <h1 class="text-2xl font-bold mb-6">
                    Invite Teammate
                </h1>


                @if (session('success'))

                    <div class="mb-4 p-4 bg-green-100 text-green-700 rounded">

                        {{ session('success') }}

                    </div>

                @endif


                <form
                    method="POST"
                    action="{{ route('invitations.store') }}"
                    class="space-y-6"
                >

                    @csrf


                    <div>

                        <label
                            for="email"
                            class="block text-sm font-medium text-gray-700"
                        >
                            Email
                        </label>

                        <input
                            id="email"
                            type="email"
                            name="email"
                            value="{{ old('email') }}"
                            required
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm"
                        >

                        @error('email')

                            <p class="mt-2 text-sm text-red-600">

                                {{ $message }}

                            </p>

                        @enderror

                    </div>


                    <div>

                        <label
                            for="role"
                            class="block text-sm font-medium text-gray-700"
                        >
                            Role
                        </label>

                        <select
                            id="role"
                            name="role"
                            required
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm"
                        >

                            <option value="member">
                                Member
                            </option>

                            <option value="admin">
                                Admin
                            </option>

                        </select>


                        @error('role')

                            <p class="mt-2 text-sm text-red-600">

                                {{ $message }}

                            </p>

                        @enderror

                    </div>


                    <div>

                        <button
                            type="submit"
                            class="px-4 py-2 bg-black text-white rounded hover:bg-gray-800"
                        >
                            Invite Teammate
                        </button>

                    </div>

                </form>

            </div>

        </div>

    </div>

</x-layouts.app>