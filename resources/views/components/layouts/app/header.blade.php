<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    {{-- CSRF token for AJAX requests --}}
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Part Timer') }}</title>

    @vite(['resources/css/app.css', 'resources/js/app.js'])

    @livewireStyles
</head>

<body class="min-h-screen bg-gray-50 text-slate-900 antialiased">

    <nav class="bg-blue-950 text-white shadow-sm">
        <div class="max-w-7xl mx-auto px-4">
            <div class="flex justify-between h-16 items-center">

                <a href="{{ route('dashboard') }}" class="text-lg font-bold tracking-tight">
                    PART TIMER
                </a>

                <div class="flex items-center gap-6">
                    @php
                        $linkClass = "text-sm font-medium hover:text-blue-300 transition-colors py-5";
                        $activeClass = "text-blue-300 border-b-2 border-blue-300 font-bold";
                    @endphp

                    <a href="{{ route('dashboard') }}"
                        class="{{ $linkClass }} {{ request()->routeIs('dashboard*') ? $activeClass : '' }}">Home</a>

                    @if(auth()->user()->user_type === 'seeker')
                        <a href="{{ route('jobs.applied') }}"
                            class="{{ $linkClass }} {{ request()->routeIs('jobs.applied') ? $activeClass : '' }}">Applied</a>
                    @else
                        <a href="{{ route('jobs.posted') }}"
                            class="{{ $linkClass }} {{ request()->routeIs('jobs.posted') ? $activeClass : '' }}">Posted</a>
                    @endif

                    <a href="{{ route('inbox') }}"
                        class="{{ $linkClass }} {{ request()->routeIs('inbox') ? $activeClass : '' }}">Inbox</a>
                    <a href="{{ route('ratings.index') }}"
                        class="{{ $linkClass }} {{ request()->routeIs('ratings.index') ? $activeClass : '' }}">Ratings</a>

                    <div class="relative ml-2" x-data="{ open: false }">
                        <button @click="open = !open" @click.away="open = false"
                            class="flex items-center gap-2 hover:text-blue-300">
                            <span class="text-sm font-medium">Profile</span>
                            <svg class="w-4 h-4 opacity-70" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path d="M19 9l-7 7-7-7" stroke-width="2" stroke-linecap="round"
                                    stroke-linejoin="round"></path>
                            </svg>
                        </button>

                        <div x-show="open" x-cloak
                            class="absolute right-0 mt-2 w-48 bg-white border border-gray-200 rounded shadow-lg py-1 z-50">
                            <a href="{{ route('profile.show') }}"
                                class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">View Profile</a>
                            <hr class="my-1 border-gray-100">
                            <form action="{{ route('logout') }}" method="POST">
                                @csrf
                                <button type="submit"
                                    class="block w-full text-left px-4 py-2 text-sm text-red-600 hover:bg-gray-100">Logout</button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </nav>

    <main>
        {{ $slot }}
    </main>


        @livewireScripts

    </body>

</html>