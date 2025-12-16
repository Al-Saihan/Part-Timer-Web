<x-layouts.app :title="__('Recruiter Dashboard')">
    <div class="min-h-screen bg-gradient-to-br from-blue-100 via-blue-50 to-blue-200">
        <!-- Simple Navbar -->
        <div class="bg-white border-b border-blue-200">
            <div class="max-w-7xl mx-auto px-4 py-4">
                <div class="flex justify-between items-center">
                    <div class="text-xl font-bold text-blue-900">Part Timer</div>
                    <div class="flex gap-4 items-center">
                        <a href="{{ route('recruiter.dashboard') }}"
                            class="text-blue-900 hover:text-blue-700 text-sm">Home</a>
                        <a href="{{ route('jobs.posted') }}" class="text-blue-900 hover:text-blue-700 text-sm">Posted Jobs</a>
                        <a href="#" class="text-blue-900 hover:text-blue-700 text-sm">Inbox</a>

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
                                <a href="#" class="block px-4 py-2 text-sm text-blue-900 hover:bg-blue-50">View Full
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

        <!-- Main Content -->
        <div class="max-w-7xl mx-auto px-4 py-8">
            <!-- User Profile -->
            <div class="bg-white rounded-lg p-6 mb-6">
                <div class="flex items-center gap-4">
                    <!-- Profile Picture -->
                    <div class="h-20 w-20 rounded-full bg-blue-200 flex items-center justify-center">
                        <span class="text-2xl font-bold text-blue-900">JD</span>
                    </div>

                    <!-- User Details -->
                    <div>
                        <h2 class="text-xl font-bold text-blue-900">{{ auth()->user()->name }}</h2>
                        <p class="text-blue-700">Recruiter</p>
                        <div class="flex gap-6 mt-2 text-sm text-blue-900">
                            <span>{{ auth()->user()->email }}</span>
                            <span>Member since {{ auth()->user()->created_at->format('M Y') }}</span>
                        </div>
                    </div>
                </div>
            </div>

            <hr class="my-6 border-blue-200">

            <!-- Jobs Applicants-->
            <h3 class="text-lg font-bold text-blue-900 mb-4 text-center">Job Applicants</h3>
            <div class="bg-white rounded-lg p-6">
                <div class="text-center py-4">
                    <div class="mx-auto h-12 w-12 rounded-full bg-blue-100 flex items-center justify-center mb-3">
                        <svg class="h-6 w-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                        </svg>
                    </div>
                    <p class="text-blue-900">No one has applied to your jobs yet</p>
                    <a href="{{ route('jobs.posted') }}"
                        class="mt-3 inline-block bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded text-sm">
                        Check Posted Jobs!
                    </a>
                </div>
            </div>
        </div>
    </div>
</x-layouts.app>