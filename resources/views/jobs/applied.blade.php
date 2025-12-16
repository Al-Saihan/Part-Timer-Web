<x-layouts.app :title="__('Applied Jobs')">
    <div class="min-h-screen bg-gradient-to-br from-blue-100 via-blue-50 to-blue-200">
        <!-- Simple Navbar -->
        <div class="bg-white border-b border-blue-200">
            <div class="max-w-7xl mx-auto px-4 py-4">
                <div class="flex justify-between items-center">
                    <div class="text-xl font-bold text-blue-900">Part Timer</div>
                    <div class="flex gap-4 items-center">
                        <a href="{{ route('seeker.dashboard') }}" class="text-blue-900 hover:text-blue-700 text-sm">Home</a>
                        <a href="{{ route('jobs.applied') }}" class="text-blue-900 hover:text-blue-700 text-sm font-semibold border-b-2 border-blue-600">Applied Jobs</a>
                        <a href="#" class="text-blue-900 hover:text-blue-700 text-sm">Inbox</a>

                        <!-- Profile Dropdown -->
                        <div class="relative" x-data="{ open: false }">
                            <button @click="open = !open" class="text-blue-900 hover:text-blue-700 text-sm flex items-center gap-1">
                                Profile
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                                </svg>
                            </button>

                            <div x-show="open" @click.away="open = false" x-cloak
                                class="absolute right-0 mt-2 w-48 bg-white rounded-md shadow-lg py-1 border border-blue-200 z-10">
                                <a href="#" class="block px-4 py-2 text-sm text-blue-900 hover:bg-blue-50">View Full Profile</a>
                                <form action="{{ route('logout') }}" method="POST">
                                    @csrf
                                    <button type="submit" class="block w-full text-left px-4 py-2 text-sm text-red-600 hover:bg-red-50">
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
            <div class="flex items-center justify-between mb-6">
                <h2 class="text-2xl font-bold text-blue-900">Jobs You've Applied To</h2>
                <a href="{{ route('seeker.dashboard') }}" class="text-blue-600 hover:text-blue-800 text-sm font-medium">← Back to Dashboard</a>
            </div>

            <div class="space-y-4">
                @forelse($applications as $application)
                    @php
                        $job = $application->job ?? null;
                        $status = $application->STATUS ?? $application->status ?? 'pending';
                    @endphp
                    <div class="bg-white rounded-lg p-6 border border-blue-200">
                        <div class="flex justify-between items-start">
                            <div class="flex-1">
                                <h3 class="text-lg font-semibold text-blue-900">{{ $job?->title ?? 'Job' }}</h3>
                                <p class="text-blue-700 text-sm mt-1">
                                    <span class="capitalize">{{ $job?->difficulty ?? 'n/a' }}</span>
                                    • {{ $job?->working_hours ?? 'n/a' }} hrs/week
                                    • ${{ isset($job->payment) ? number_format($job->payment, 2) : '0.00' }}
                                </p>
                                @if(!empty($job?->description))
                                <p class="text-blue-600 text-sm mt-3">{{ \Illuminate\Support\Str::limit($job->description, 140) }}</p>
                                @endif
                                <div class="flex items-center gap-4 mt-3 text-xs text-blue-500">
                                    <span>Applied {{ \Carbon\Carbon::parse($application->applied_at ?? now())->diffForHumans() }}</span>
                                    @if(isset($job->created_at))
                                        <span>• Posted {{ \Carbon\Carbon::parse($job->created_at)->diffForHumans() }}</span>
                                    @endif
                                </div>
                            </div>
                            <div class="text-right">
                                <span class="px-3 py-1 text-xs rounded-full 
                                    {{ $status === 'pending' ? 'bg-yellow-100 text-yellow-800' : '' }}
                                    {{ $status === 'accepted' ? 'bg-green-100 text-green-800' : '' }}
                                    {{ $status === 'rejected' ? 'bg-red-100 text-red-800' : '' }}">
                                    {{ ucfirst($status) }}
                                </span>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="bg-white rounded-lg p-8 text-center border border-blue-200">
                        <div class="mx-auto h-16 w-16 rounded-full bg-blue-100 flex items-center justify-center mb-4">
                            <svg class="h-8 w-8 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                            </svg>
                        </div>
                        <p class="text-blue-900 font-semibold text-lg">You haven't applied to any jobs yet</p>
                        <p class="text-blue-600 text-sm mt-2">Browse jobs on your dashboard and apply to get started!</p>
                        <a href="{{ route('seeker.dashboard') }}" class="mt-4 inline-block bg-blue-600 hover:bg-blue-700 text-white px-6 py-2 rounded text-sm font-medium">Go to Dashboard</a>
                    </div>
                @endforelse
            </div>
        </div>
    </div>
</x-layouts.app>
