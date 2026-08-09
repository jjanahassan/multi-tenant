<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Dashboard') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <p>You're logged in!</p>
                    
                    <!-- Company Info -->
                    <div class="mt-4">
                        <p><strong>Company:</strong> {{ auth()->user()->company->name ?? 'No Company' }}</p>
                        <p><strong>Email:</strong> {{ auth()->user()->email }}</p>
                    </div>

                    <!-- Remove this if you want only the dropdown logout -->
                    <!-- The logout button in the dropdown is enough -->
                </div>
            </div>
        </div>
    </div>
</x-app-layout>