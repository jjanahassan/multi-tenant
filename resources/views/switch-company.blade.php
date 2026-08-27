<x-layouts.app title="Switch Company">

    <div class="p-6 max-w-7xl mx-auto">

        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">

            <div class="p-6 text-gray-900">

                <div class="mb-6">

                    <h1 class="text-2xl font-bold">
                        Switch Company
                    </h1>

                    <h3 class="text-lg font-semibold text-gray-700 mt-6">
                        Current Company
                    </h3>

                    <p class="text-gray-600 mt-1">

                        <strong>
                            {{ auth()->user()->company->name ?? 'No Company' }}
                        </strong>

                        <span class="ml-2 text-sm text-green-600">
                            (Active)
                        </span>

                    </p>

                    @if(auth()->user()->company)

                        <p class="text-sm text-gray-500 mt-1">

                            Company Owner:

                            {{ auth()->user()->company->owner->name ?? 'N/A' }}

                        </p>

                    @endif

                </div>


                <div class="border-t border-gray-200 pt-6">

                    <h3 class="text-lg font-semibold text-gray-700">
                        Available Companies
                    </h3>

                    @php
                        $companies = auth()->user()->company
                            ? [auth()->user()->company]
                            : [];
                    @endphp


                    @if(count($companies) > 1)

                        <div class="mt-4 space-y-3">

                            @foreach($companies as $company)

                                <div
                                    class="flex items-center justify-between p-3 bg-gray-50 rounded-lg hover:bg-gray-100 transition"
                                >

                                    <div>

                                        <p class="font-medium text-gray-800">
                                            {{ $company->name }}
                                        </p>

                                        @if($company->id === auth()->user()->company_id)

                                            <span class="text-xs text-green-600">
                                                ✓ Current Company
                                            </span>

                                        @endif

                                    </div>


                                    @if($company->id !== auth()->user()->company_id)

                                        <form
                                            method="POST"
                                            action="{{ route('switch-company.switch') }}"
                                        >

                                            @csrf

                                            <input
                                                type="hidden"
                                                name="company_id"
                                                value="{{ $company->id }}"
                                            >

                                            <button
                                                type="submit"
                                                class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded text-sm"
                                            >
                                                Switch
                                            </button>

                                        </form>

                                    @else

                                        <span class="text-gray-400 text-sm">
                                            Active
                                        </span>

                                    @endif

                                </div>

                            @endforeach

                        </div>

                    @else

                        <div
                            class="mt-4 p-4 bg-blue-50 border border-blue-200 rounded-lg"
                        >

                            <p class="text-blue-700">

                                <strong>
                                    Switch Company
                                </strong>

                            </p>

                            <p class="text-blue-600 text-sm mt-1">

                                You currently have only one company.
                                Switch company will be available when you
                                have multiple companies.

                            </p>

                        </div>

                    @endif

                </div>


                <div class="border-t border-gray-200 pt-6 mt-6">

                    <a
                        href="{{ route('dashboard') }}"
                        class="text-blue-500 hover:text-blue-700"
                    >
                        ← Back to Dashboard
                    </a>

                </div>

            </div>

        </div>

    </div>

</x-layouts.app>