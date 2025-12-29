<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        @include('partials.head')
    </head>
    <body class="min-h-screen bg-white text-blue-900">
        <!-- Simple Navbar -->
        <div class="bg-white border-b border-blue-200">
            <div class="max-w-7xl mx-auto px-4 py-4">
                <div class="flex justify-between items-center">
                    <a href="{{ route('dashboard') }}" class="text-xl font-bold text-blue-900 {{ (request()->routeIs('dashboard') || request()->routeIs('seeker.dashboard') || request()->routeIs('recruiter.dashboard')) ? 'text-blue-700 font-semibold border-b-2 border-blue-700 pb-1' : '' }}">Part Timer</a>
                    <div class="flex gap-4 items-center">
                        <a href="{{ route('dashboard') }}" class="text-blue-900 hover:text-blue-700 text-sm {{ (request()->routeIs('dashboard') || request()->routeIs('seeker.dashboard') || request()->routeIs('recruiter.dashboard')) ? 'text-blue-700 font-semibold border-b-2 border-blue-700 pb-1' : '' }}">Home</a>
                        @if(auth()->user()->user_type === 'seeker')
                            <a href="{{ route('jobs.applied') }}" class="text-blue-900 hover:text-blue-700 text-sm {{ request()->routeIs('jobs.applied') ? 'text-blue-700 font-semibold border-b-2 border-blue-700 pb-1' : '' }}">Applied Jobs</a>
                        @elseif(auth()->user()->user_type === 'recruiter')
                            <a href="{{ route('jobs.posted') }}" class="text-blue-900 hover:text-blue-700 text-sm {{ request()->routeIs('jobs.posted') ? 'text-blue-700 font-semibold border-b-2 border-blue-700 pb-1' : '' }}">Posted Jobs</a>
                        @endif
                        <a href="{{ route('inbox') }}" class="text-blue-900 hover:text-blue-700 text-sm {{ request()->routeIs('inbox') ? 'text-blue-700 font-semibold border-b-2 border-blue-700 pb-1' : '' }}">Inbox</a>
                        <a href="{{ route('ratings.index') }}" class="text-blue-900 hover:text-blue-700 text-sm {{ request()->routeIs('ratings.index') ? 'text-blue-700 font-semibold border-b-2 border-blue-700 pb-1' : '' }}">Ratings</a>

                        <!-- Profile Dropdown -->
                        <div class="relative" x-data="{ open: false }">
                            <button @click="open = !open"
                                class="text-blue-900 hover:text-blue-700 text-sm flex items-center gap-1">
                                Profile
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M19 9l-7 7-7-7"></path>
                                </svg>
                            </button>

                            <div x-show="open" @click.away="open = false" x-cloak
                                class="absolute right-0 mt-2 w-48 bg-white rounded-md shadow-lg py-1 border border-blue-200 z-10">
                                <a href="{{ route('profile.show') }}" class="block px-4 py-2 text-sm text-blue-900 hover:bg-blue-50">View Full
                                    Profile</a>
                                <form action="{{ route('logout') }}" method="POST">
                                    @csrf
                                    <button type="submit"
                                        class="block w-full text-left px-4 py-2 text-sm text-red-600 hover:bg-red-50">
                                        Logout
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <main class="flex-1">
            {{ $slot }}
        </main>
    </body>
</html>
