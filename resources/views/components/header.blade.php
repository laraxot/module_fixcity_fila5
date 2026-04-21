<x-slot name="header">
    <!-- Header based on authentication status -->
    <div class="flex items-center justify-between px-4 py-3 bg-white border-b">
        @auth
            <!-- Authenticated user header -->
            <div class="flex items-center space-x-3">
                <!-- Avatar -->
                <div class="relative h-10 w-10">
                    <img src="{{ asset('storage/profile_photos/' . auth()->user()->id . '.jpg') }}"
                         alt="Avatar"
                         class="h-10 w-10 rounded-full object-cover"
                         onerror="this.onerror=null;this.src='{{ asset('images/default-avatar.png') }}';">
                    <span class="absolute -bottom-1 -right-0 h-2.5 w-2.5 bg-green-500 rounded-full border-2 border-white"></span>
                </div>
                <div>
                    <p class="text-sm font-medium text-gray-900">{{ auth()->user()->name }}</p>
                    <p class="text-xs text-gray-500">{{ auth()->user()->email }}</p>
                </div>
                <!-- Dropdown trigger -->
                <div class="relative ml-3">
                    <div class="flex items-center text-sm text-gray-500 hover:text-gray-900 cursor-pointer">
                        <svg class="h-4 w-4 mr-1" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                        </svg>
                    </div>
                    <!-- Dropdown menu -->
                    <div class="absolute right-0 mt-2 w-56 origin-top-right rounded-md bg-white shadow-lg ring-1 ring-black ring-opacity-5 focus:outline-none z-20"
                         x-show="open"
                         x-on:click.away="open = false"
                         x-cloak>
                        <div class="py-1" role="menu" aria-orientation="vertical" aria-labelledby="user-menu">
                            <!-- Services -->
                            <a href="{{ url('/services') }}"
                               class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100"
                               role="menuitem">
                                Servizi
                            </a>
                            <!-- Practices -->
                            <a href="{{ url('/practices') }}"
                               class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100"
                               role="menuitem">
                                Pratiche
                            </a>
                            <!-- Notifications -->
                            <a href="{{ url('/notifications') }}"
                               class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100"
                               role="menuitem">
                                Notifiche
                            </a>
                            <!-- Settings -->
                            <a href="{{ url('/settings') }}"
                               class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100"
                               role="menuitem">
                                Impostazioni
                            </a>
                            <div class="-my-1 border-0" role="none">
                                <div class="px-2 py-1 border-t" aria-hidden="true"></div>
                            </div>
                            <!-- Logout -->
                            <form method="POST" action="{{ route('logout') }}" class="block">
                                @csrf
                                <button type="submit"
                                        class="block w-full text-left px-4 py-2 text-sm text-gray-700 hover:bg-gray-100"
                                        role="menuitem">
                                    Logout
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        @else
            <!-- Guest user header -->
            <div class="text-sm text-gray-600">
                Accedi all'area personale
            </div>
        @endauth
    </div>
</x-slot>