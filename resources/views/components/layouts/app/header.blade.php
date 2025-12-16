<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="dark">
    <head>
        @include('partials.head')
    </head>
    <body class="min-h-screen bg-white text-zinc-900 dark:bg-zinc-900 dark:text-zinc-100">
        <div class="min-h-screen flex flex-col">
            <header class="border-b border-zinc-200 bg-zinc-50 px-4 py-3 dark:border-zinc-800 dark:bg-zinc-900">
                <div class="mx-auto flex max-w-7xl items-center justify-between gap-4">
                    <div class="flex items-center gap-3">
                        <button
                            type="button"
                            class="rounded-md border border-transparent px-3 py-2 text-sm font-medium text-zinc-700 hover:bg-zinc-100 focus:outline-none focus:ring-2 focus:ring-accent dark:text-zinc-200 lg:hidden"
                            data-open-mobile-menu
                        >
                            Menu
                        </button>

                        <a href="{{ route('dashboard') }}" class="flex items-center space-x-2 rtl:space-x-reverse">
                            <x-app-logo class="h-8 w-8" />
                            <span class="text-base font-semibold">{{ config('app.name', 'Laravel') }}</span>
                        </a>

                        <nav class="hidden items-center gap-2 lg:flex">
                            <a
                                href="{{ route('dashboard') }}"
                                class="rounded-md px-3 py-2 text-sm font-medium hover:bg-zinc-100 dark:hover:bg-zinc-800 {{ request()->routeIs('dashboard') ? 'bg-zinc-100 dark:bg-zinc-800' : '' }}"
                            >
                                {{ __('Dashboard') }}
                            </a>
                        </nav>
                    </div>

                    <div class="flex items-center gap-3 text-sm">
                        <div class="hidden items-center gap-2 sm:flex">
                            <a
                                href="https://github.com/laravel/livewire-starter-kit"
                                target="_blank"
                                class="rounded-md px-3 py-2 font-medium hover:bg-zinc-100 dark:hover:bg-zinc-800"
                            >
                                {{ __('Repository') }}
                            </a>
                            <a
                                href="https://laravel.com/docs/starter-kits#livewire"
                                target="_blank"
                                class="rounded-md px-3 py-2 font-medium hover:bg-zinc-100 dark:hover:bg-zinc-800"
                            >
                                {{ __('Documentation') }}
                            </a>
                        </div>

                        <div class="relative" data-user-menu>
                            <button
                                type="button"
                                class="flex items-center gap-2 rounded-md px-2 py-1 font-medium hover:bg-zinc-100 focus:outline-none focus:ring-2 focus:ring-accent dark:hover:bg-zinc-800"
                                data-user-menu-button
                            >
                                <span class="flex h-9 w-9 items-center justify-center rounded-full bg-zinc-200 text-sm font-semibold text-zinc-900 dark:bg-zinc-800 dark:text-zinc-100">
                                    {{ auth()->user()->initials() }}
                                </span>
                                <span class="hidden text-left leading-tight sm:block">
                                    <span class="block text-sm font-semibold">{{ auth()->user()->name }}</span>
                                    <span class="block text-xs text-zinc-500 dark:text-zinc-400">{{ auth()->user()->email }}</span>
                                </span>
                            </button>

                            <div
                                class="absolute right-0 mt-2 w-56 origin-top-right divide-y divide-zinc-200 rounded-md border border-zinc-200 bg-white shadow-lg dark:divide-zinc-800 dark:border-zinc-800 dark:bg-zinc-900"
                                data-user-menu-panel
                                hidden
                            >
                                <div class="px-3 py-3 text-sm">
                                    <div class="font-semibold">{{ auth()->user()->name }}</div>
                                    <div class="truncate text-xs text-zinc-500 dark:text-zinc-400">{{ auth()->user()->email }}</div>
                                </div>
                                <div class="py-1 text-sm">
                                    <a
                                        href="{{ route('profile.edit') }}"
                                        class="block px-3 py-2 hover:bg-zinc-100 dark:hover:bg-zinc-800"
                                    >
                                        {{ __('Settings') }}
                                    </a>
                                    <form method="POST" action="{{ route('logout') }}">
                                        @csrf
                                        <button
                                            type="submit"
                                            class="block w-full px-3 py-2 text-left hover:bg-zinc-100 dark:hover:bg-zinc-800"
                                            data-test="logout-button"
                                        >
                                            {{ __('Log Out') }}
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </header>

            <div
                id="mobile-menu"
                class="fixed inset-0 z-40 hidden bg-black/40 lg:hidden"
                aria-hidden="true"
            >
                <div class="h-full w-72 border-e border-zinc-200 bg-zinc-50 p-4 text-sm shadow-lg dark:border-zinc-800 dark:bg-zinc-950">
                    <div class="flex items-center justify-between">
                        <a href="{{ route('dashboard') }}" class="flex items-center space-x-2 rtl:space-x-reverse">
                            <x-app-logo class="h-8 w-8" />
                            <span class="text-base font-semibold">{{ config('app.name', 'Laravel') }}</span>
                        </a>
                        <button
                            type="button"
                            class="rounded-md px-3 py-2 text-sm font-medium text-zinc-700 hover:bg-zinc-100 focus:outline-none focus:ring-2 focus:ring-accent dark:text-zinc-200"
                            data-close-mobile-menu
                        >
                            Close
                        </button>
                    </div>

                    <div class="mt-6 space-y-1">
                        <a
                            href="{{ route('dashboard') }}"
                            class="block rounded-md px-3 py-2 font-medium hover:bg-zinc-100 dark:hover:bg-zinc-800 {{ request()->routeIs('dashboard') ? 'bg-zinc-100 dark:bg-zinc-800' : '' }}"
                        >
                            {{ __('Dashboard') }}
                        </a>
                        <a
                            href="https://github.com/laravel/livewire-starter-kit"
                            target="_blank"
                            class="block rounded-md px-3 py-2 font-medium hover:bg-zinc-100 dark:hover:bg-zinc-800"
                        >
                            {{ __('Repository') }}
                        </a>
                        <a
                            href="https://laravel.com/docs/starter-kits#livewire"
                            target="_blank"
                            class="block rounded-md px-3 py-2 font-medium hover:bg-zinc-100 dark:hover:bg-zinc-800"
                        >
                            {{ __('Documentation') }}
                        </a>
                        <a
                            href="{{ route('profile.edit') }}"
                            class="block rounded-md px-3 py-2 font-medium hover:bg-zinc-100 dark:hover:bg-zinc-800"
                        >
                            {{ __('Settings') }}
                        </a>
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button
                                type="submit"
                                class="block w-full rounded-md px-3 py-2 text-left font-medium hover:bg-zinc-100 dark:hover:bg-zinc-800"
                                data-test="logout-button"
                            >
                                {{ __('Log Out') }}
                            </button>
                        </form>
                    </div>
                </div>
            </div>

            <main class="flex-1 bg-white dark:bg-zinc-900">
                {{ $slot }}
            </main>
        </div>

        <script>
            // Mobile menu toggle
            const mobileMenu = document.getElementById('mobile-menu');
            const openMobileBtn = document.querySelector('[data-open-mobile-menu]');
            const closeMobileBtn = document.querySelector('[data-close-mobile-menu]');

            const openMobileMenu = () => mobileMenu?.classList.remove('hidden');
            const closeMobileMenu = () => mobileMenu?.classList.add('hidden');

            openMobileBtn?.addEventListener('click', openMobileMenu);
            closeMobileBtn?.addEventListener('click', closeMobileMenu);
            mobileMenu?.addEventListener('click', (event) => {
                if (event.target === mobileMenu) {
                    closeMobileMenu();
                }
            });

            // User menu toggle
            const userMenuButton = document.querySelector('[data-user-menu-button]');
            const userMenuPanel = document.querySelector('[data-user-menu-panel]');

            const closeUserMenu = () => userMenuPanel?.setAttribute('hidden', '');
            const toggleUserMenu = () => {
                if (!userMenuPanel) return;
                const isHidden = userMenuPanel.hasAttribute('hidden');
                if (isHidden) {
                    userMenuPanel.removeAttribute('hidden');
                } else {
                    closeUserMenu();
                }
            };

            userMenuButton?.addEventListener('click', (event) => {
                event.stopPropagation();
                toggleUserMenu();
            });

            document.addEventListener('click', (event) => {
                if (!userMenuPanel || userMenuPanel.hasAttribute('hidden')) return;
                const withinMenu = event.target.closest('[data-user-menu]');
                if (!withinMenu) {
                    closeUserMenu();
                }
            });
        </script>
    </body>
</html>
