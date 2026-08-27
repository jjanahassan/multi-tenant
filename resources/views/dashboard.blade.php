<x-layouts.app title="Dashboard">

    <div class="p-6 max-w-7xl mx-auto">

        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">

            <div class="p-6 text-gray-900">

                <h1 class="text-2xl font-bold mb-4">
                    Dashboard
                </h1>

                <p>You're logged in!</p>

                <!-- Company Info -->
                <div class="mt-4">
                    <p>
                        <strong>Company:</strong>
                        {{ auth()->user()->company->name ?? 'No Company' }}
                    </p>

                    <p>
                        <strong>Email:</strong>
                        {{ auth()->user()->email }}
                    </p>
                </div>

                

            </div>

        </div>

    </div>

</x-layouts.app>