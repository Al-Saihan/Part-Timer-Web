<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="dark">
    <head>
        @include('partials.head')
    </head>
    <body class="min-h-screen bg-white text-zinc-900 dark:bg-zinc-900 dark:text-zinc-100">
        <div class="flex min-h-screen">
            <aside
                id="app-sidebar"
                class="hidden w-72 shrink-0 flex-col border-e border-zinc-200 bg-zinc-50 transition-transform duration-200 ease-out dark:border-zinc-800 dark:bg-zinc-950 lg:flex"
            >
                <div class="flex items-center justify-between px-4 py-4 lg:justify-start lg:space-x-3">
                    <a href="{{ route('dashboard') }}" class="flex items-center space-x-2 rtl:space-x-reverse">
                        <x-app-logo />
                        <span class="text-lg font-semibold">{{ config('app.name', 'Laravel') }}</span>
                    </a>

                    <button
                        type="button"
                        class="rounded-md p-2 text-sm text-zinc-600 hover:bg-zinc-100 focus:outline-none focus:ring-2 focus:ring-accent lg:hidden"
                        data-close-sidebar
                    >
                        ✕
                    </button>
                </div>

                <nav class="px-3 pb-4">
                    <div class="px-2 pb-1 text-xs font-semibold uppercase tracking-wide text-zinc-500">{{ __('Platform') }}</div>
                    <ul class="space-y-1">
                        <li>
                            <a
                                href="{{ route('dashboard') }}"
                                class="flex items-center rounded-md px-3 py-2 text-sm font-medium hover:bg-zinc-100 dark:hover:bg-zinc-800 {{ request()->routeIs('dashboard') ? 'bg-zinc-100 dark:bg-zinc-800' : '' }}"
                            >
                                {{ __('Dashboard') }}
                            </a>
                        </li>
                    </ul>

                    <div class="mt-6 px-2 pb-1 text-xs font-semibold uppercase tracking-wide text-zinc-500">{{ __('Resources') }}</div>
                    <ul class="space-y-1">
                        <li>
                            <a
                                href="https://github.com/laravel/livewire-starter-kit"
                                target="_blank"
                                class="flex items-center rounded-md px-3 py-2 text-sm font-medium hover:bg-zinc-100 dark:hover:bg-zinc-800"
                            >
                                {{ __('Repository') }}
                            </a>
                        </li>
                        <li>
                            <a
                                href="https://laravel.com/docs/starter-kits#livewire"
                                target="_blank"
                                class="flex items-center rounded-md px-3 py-2 text-sm font-medium hover:bg-zinc-100 dark:hover:bg-zinc-800"
                            >
                                {{ __('Documentation') }}
                            </a>
                        </li>
                    </ul>
                </nav>

                <div class="mt-auto border-t border-zinc-200 px-4 py-4 text-sm dark:border-zinc-800">
                    <div class="flex items-center gap-3">
                        <span class="flex h-10 w-10 items-center justify-center rounded-full bg-zinc-200 text-sm font-semibold text-zinc-900 dark:bg-zinc-800 dark:text-zinc-100">
                            {{ auth()->user()->initials() }}
                        </span>
                        <div class="leading-tight">
                            <div class="font-semibold">{{ auth()->user()->name }}</div>
                            <div class="text-xs text-zinc-500 dark:text-zinc-400">{{ auth()->user()->email }}</div>
                        </div>
                    </div>

                    <div class="mt-3 space-y-1">
                        <a
                            href="{{ route('profile.edit') }}"
                            class="block rounded-md px-3 py-2 text-sm font-medium hover:bg-zinc-100 dark:hover:bg-zinc-800"
                        >
                            {{ __('Settings') }}
                        </a>

                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button
                                type="submit"
                                class="block w-full rounded-md px-3 py-2 text-left text-sm font-medium hover:bg-zinc-100 dark:hover:bg-zinc-800"
                                data-test="logout-button"
                            >
                                {{ __('Log Out') }}
                            </button>
                        </form>
                    </div>
                </div>
            </aside>

            <div class="flex min-h-screen flex-1 flex-col">
                <header class="flex items-center justify-between border-b border-zinc-200 bg-white px-4 py-3 dark:border-zinc-800 dark:bg-zinc-900 lg:hidden">
                    <div class="flex items-center gap-3">
                        <button
                            type="button"
                            class="rounded-md p-2 text-zinc-700 hover:bg-zinc-100 focus:outline-none focus:ring-2 focus:ring-accent dark:text-zinc-200"
                            data-open-sidebar
                        >
                            ☰
                        </button>
                        <a href="{{ route('dashboard') }}" class="flex items-center space-x-2 rtl:space-x-reverse">
                            <x-app-logo class="h-8 w-8" />
                            <span class="text-base font-semibold">{{ config('app.name', 'Laravel') }}</span>
                        </a>
                    </div>

                    <div class="flex items-center gap-3 text-sm">
                        <div class="text-right leading-tight">
                            <div class="font-semibold">{{ auth()->user()->name }}</div>
                            <div class="text-xs text-zinc-500 dark:text-zinc-400">{{ auth()->user()->email }}</div>
                        </div>
                        <span class="flex h-10 w-10 items-center justify-center rounded-full bg-zinc-200 text-sm font-semibold text-zinc-900 dark:bg-zinc-800 dark:text-zinc-100">
                            {{ auth()->user()->initials() }}
                        </span>
                    </div>
                </header>

                <main class="flex-1 bg-white dark:bg-zinc-900">
                    {{ $slot }}
                </main>
            </div>
        </div>

        <script>
            // Minimal sidebar toggle for mobile
            const sidebar = document.getElementById('app-sidebar');
            const openBtn = document.querySelector('[data-open-sidebar]');
            const closeBtn = document.querySelector('[data-close-sidebar]');

            const openSidebar = () => {
                if (sidebar) sidebar.classList.remove('hidden');
            };

            const closeSidebar = () => {
                if (sidebar && window.innerWidth < 1024) {
                    sidebar.classList.add('hidden');
                }
            };

            openBtn?.addEventListener('click', openSidebar);
            closeBtn?.addEventListener('click', closeSidebar);
            window.addEventListener('resize', () => {
                if (window.innerWidth >= 1024) {
                    sidebar?.classList.remove('hidden');
                } else {
                    sidebar?.classList.add('hidden');
                }
            });
        </script>
    </body>
</html>
