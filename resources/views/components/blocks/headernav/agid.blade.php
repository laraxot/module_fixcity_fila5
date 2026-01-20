<header class="text-white bg-emerald-700">
    <div class="h-12 bg-emerald-900 min-h-12 navbar">
        <div class="flex justify-between w-full max-w-screen-xl mx-auto">
            <div class="flex-1 py-1">
                <a class="text-sm" href="#">Nome della Regione</a>
            </div>
            <div class="flex-none">
                <ul class="px-1 menu menu-horizontal">
                    <livewire:dark-mode-switcher />
                    <livewire:lang.switcher />
                    {{-- <li>
						<details>
							<summary>ITA</summary>
							<ul class="p-2 !mt-0 bg-white text-gray-950 rounded">
								<li><a>ITA</a></li>
								<li><a>ENG</a></li>
							</ul>
						</details>
					</li> --}}
                    @guest
                        <li class="ml-4">
                            <a class="flex items-center space-x-1" href="{{ route('login') }}">
                                <x-heroicon-o-user class="size-4" />
                                <div class="hidden md:block">Accedi all'area personale</div>
                            </a>
                        </li>
                    @endguest

                    <!-- Show user dropdown when authenticated -->
                    @auth
                        <button id="dropdownDefaultButton" data-dropdown-toggle="dropdown"
                            class="flex items-center space-x-1 cursor-pointer ml-4" type="button">
                            <x-heroicon-o-user class="size-4" />
                            <div class="hidden md:block">{{ Auth::user()->name }}</div>
                            <svg class="size-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                            </svg>
                        </button>

                        <!-- Dropdown menu -->
                        <div id="dropdown"
                            class="z-10 hidden bg-white divide-y divide-gray-100 rounded-lg shadow-sm w-44 dark:bg-gray-700">
                            <ul class="py-2 text-sm text-gray-700 dark:text-gray-200"
                                aria-labelledby="dropdownDefaultButton">
                                <li>
                                    <a href="#"
                                        class="block px-4 py-2 text-green-700 hover:underline dark:hover:bg-gray-600 dark:hover:text-white">I
                                        miei servizi</a>
                                </li>
                                <li>
                                    <a href="#"
                                        class="block px-4 py-2 text-green-700 hover:underline dark:hover:bg-gray-600 dark:hover:text-white">Le
                                        mie pratiche</a>
                                </li>
                                <li>
                                    <a href="#"
                                        class="block px-4 py-2 text-green-700 hover:underline dark:hover:bg-gray-600 dark:hover:text-white">Notifiche</a>
                                </li>
								<hr>
                                <li>
                                    <a href="#"
                                        class="block px-4 py-2 text-green-700 hover:underline dark:hover:bg-gray-600 dark:hover:text-white">Impostazioni</a>
                                </li>
                                <li>
                                    <form method="POST" action="{{ route('logout') }}">
                                        @csrf
                                        <button type="submit"
                                            class="block text-green-700 hover:underline dark:hover:bg-gray-600 dark:hover:text-white flex items-center space-x-2">
                                            <svg class="size-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M17 16l4-4m0 0l-4-4m4 4H7" />
                                            </svg>
                                            <span>Esci</span>
                                        </button>
                                    </form>
                                </li>
                            </ul>
                        </div>
                    @endauth

                    {{-- <li>
						<a class="flex items-center space-x-1" href="{{ route('login') }}">
							<x-heroicon-o-user class="size-4" />
							<div class="hidden md:block">Accedi all'area personale</div>
						</a>
					</li> --}}
                </ul>
            </div>
        </div>
    </div>
    <div class="navbar">
        <div class="flex justify-between w-full max-w-screen-xl mx-auto">
            <div class="flex-1">
                <a href="" class="flex items-center py-2 space-x-4">
                    <x-heroicon-o-shield-check class="stroke-1 size-16" />
                    <div class="text-start">
                        <div class="text-2xl font-bold">Il mio Comune</div>
                        <div class="text-sm">Un comune da vivere</div>
                    </div>
                </a>
            </div>
            <div class="flex-none">
                <ul class="items-center hidden px-1 menu menu-horizontal md:inline-flex md:me-2">
                    <li> <a>Seguici su</a> </li>
                    @foreach (['facebook', 'twitter', 'instagram', 'linkedin'] as $i)
                        <li>
                            <a class="p-2">
                                <x-heroicon-o-link class="size-5" />
                            </a>
                        </li>
                    @endforeach
                </ul>
                <ul class="items-center px-1 menu menu-horizontal">
                    <li class="hidden sm:block">Cerca</li>
                    <li class="ms-2">
                        <a class="bg-white border-0 btn btn-circle hover:bg-emerald-50 text-emerald-800">
                            <x-heroicon-o-magnifying-glass class="size-5" />
                        </a>
                    </li>
                </ul>
            </div>
        </div>
    </div>
    <div class="hidden h-12 overflow-auto min-h-12 navbar md:flex">
        <div class="flex justify-between w-full max-w-screen-xl mx-auto space-x-12">
            <div class="flex-1 ">
                <ul class="items-center px-1 menu menu-horizontal flex-nowrap">
                    <li><a href="">Amministrazione</a></li>
                    <li><a href="">Novità</a></li>
                    <li><a href="">Servizi</a></li>
                    <li><a href="">Vivere il Comune</a></li>
                </ul>
            </div>
            <div class="flex-none">
                <ul class="items-center px-1 menu menu-horizontal flex-nowrap">
                    <li><a href="">Iscrizioni</a></li>
                    <li><a href="">Estate in città</a></li>
                    <li><a href="">Polizia locale</a></li>
                    <li>
                        <a href="">
                            <div>Tutti gli argomenti</div>
                            <x-heroicon-o-chevron-right class="size-4" />
                        </a>
                    </li>
                </ul>
            </div>
        </div>
    </div>
</header>
