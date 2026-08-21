<nav x-data="{ open: false }" class="bg-white border-b border-gray-100">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between h-16">
            <!-- Left Side: Logo -->
            <div class="flex items-center">
                <a href="{{ route('dashboard') }}" class="text-xl font-bold text-gray-800">
                    Multi-Tenant
                </a>
            </div>

            <!-- Right Side: User Dropdown -->
            <div class="flex items-center space-x-4">
                <!-- Company Name -->
                <span class="text-sm text-gray-600">
                    {{ auth()->user()->company->name ?? 'No Company' }}
                </span>

                <!-- User Dropdown -->
                <div class="relative" x-data="{ open: false }">
                    <button @click="open = !open" class="flex items-center space-x-2 text-sm text-gray-700 hover:text-gray-900 focus:outline-none">
                        <span>{{ auth()->user()->name }}</span>
                        <svg class="h-4 w-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                        </svg>
                    </button>

                    <!-- Dropdown Menu -->
                    <div x-show="open" @click.away="open = false" class="absolute right-0 mt-2 w-48 bg-white rounded-md shadow-lg py-1 z-50">
                        <!-- Switch Company -->
                        <x-nav-link :href="route('switch-company')" :active="request()->routeIs('switch-company')">
                            {{ __('Switch Company') }}
                        </x-nav-link>

                        @can('invite', auth()->user()->company)
                            <x-nav-link :href="route('invitations.create')" :active="request()->routeIs('invitations.*')">
                                {{ __('Invite Teammate') }}
                            </x-nav-link>
                        @endcan

                        <x-nav-link
                            :href="route('projects.index')"
                            :active="request()->routeIs('projects.*')"
                        >
                            {{ __('Projects') }}
                        </x-nav-link>

                        <hr class="my-1">

                        <!-- Logout -->
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button type="submit" class="block w-full text-left px-4 py-2 text-sm text-red-600 hover:bg-gray-100">
                                Log Out
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</nav>

<!-- Alpine.js for dropdown -->
<script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>