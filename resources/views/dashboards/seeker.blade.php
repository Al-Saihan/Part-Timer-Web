<x-layouts.app :title="__('Job Seeker Dashboard')">
    <div class="min-h-screen bg-gradient-to-br from-blue-100 via-blue-50 to-blue-200">
        <!-- Navbar moved to shared header component -->

        <!-- Main Content -->
        <div class="max-w-7xl mx-auto px-4 py-8">
            <!-- User Profile -->
            <div class="bg-white rounded-lg p-6 mb-6">
                <div class="flex items-center gap-4">
                    <!-- Profile Picture -->
                    @php
                        $userPic = auth()->user()->profile_pic ?? null;
                        if ($userPic && ! str_contains($userPic, '.')) {
                            $userPic = $userPic . '.png';
                        }
                    @endphp
                    @if($userPic && file_exists(public_path('avatars/'.$userPic)))
                        <img src="{{ asset('avatars/'.$userPic) }}" alt="{{ auth()->user()->name }}" class="h-20 w-20 rounded-full object-cover" />
                    @else
                        <div class="h-20 w-20 rounded-full bg-blue-200 flex items-center justify-center">
                            <span class="text-2xl font-bold text-blue-900">{{ strtoupper(substr(auth()->user()->name ?? 'U', 0, 1)) }}</span>
                        </div>
                    @endif

                    <!-- User Details -->
                    <div>
                        <h2 class="text-xl font-bold text-blue-900">{{ auth()->user()->name }}</h2>
                        <p class="text-blue-700">Job Seeker</p>
                        <div class="flex gap-6 mt-2 text-sm text-blue-900">
                            <span>{{ auth()->user()->email }}</span>
                            <span>Member since {{ auth()->user()->created_at->format('M Y') }}</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Available Jobs Section -->
            <h3 class="text-lg font-bold text-blue-900 mb-4">Available Jobs</h3>
            <!-- Success/Error Messages -->
            @if(session('success'))
                <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">
                    {{ session('success') }}
                </div>
            @endif
            @if(session('error'))
                <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
                    {{ session('error') }}
                </div>
            @endif
            <div class="space-y-4">
                @forelse($jobs as $job)
                    <div class="bg-white rounded-lg p-4 border border-blue-200">
                        <div class="flex justify-between items-start">
                            <div class="flex-1">
                                <h4 class="font-medium text-blue-900">{{ $job->title }}</h4>
                                <p class="text-blue-700 text-sm mt-1">
                                    <span class="capitalize">{{ $job->difficulty }}</span> •
                                    {{ $job->working_hours }} hrs/day •
                                    {{ $job->location ?? 'Not Given' }} •
                                    ${{ number_format($job->payment, 2) }}
                                </p>
                                <p class="text-blue-600 text-sm mt-2">{{ Str::limit($job->description, 100) }}</p>
                                <p class="text-blue-500 text-xs mt-2">Posted {{ $job->created_at->diffForHumans() }}</p>
                            </div>
                            <div x-data="{ open: false }" class="relative">
                                <button @click="open = true" type="button" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded text-sm whitespace-nowrap">View Job</button>

                                <!-- Modal -->
                                <div x-show="open" x-cloak class="fixed inset-0 z-50 flex items-center justify-center">
                                    <div class="fixed inset-0 bg-black/40" @click="open = false"></div>

                                    <div class="bg-white rounded-lg max-w-2xl w-full mx-4 z-50 overflow-auto" @keydown.window.escape="open = false">
                                        <div class="p-6">
                                            <div class="flex items-start gap-4">
                                                @php
                                                    $rec = $job->recruiter ?? null;
                                                    $recPic = $rec?->profile_pic ?? null;
                                                    if ($recPic && ! str_contains($recPic, '.')) { $recPic = $recPic . '.png'; }
                                                @endphp
                                                @if($recPic && file_exists(public_path('avatars/'.$recPic)))
                                                    <img src="{{ asset('avatars/'.$recPic) }}" alt="{{ $rec?->name ?? 'Recruiter' }}" class="h-20 w-20 rounded-full object-cover" />
                                                @else
                                                    <div class="h-20 w-20 rounded-full bg-blue-200 flex items-center justify-center">
                                                        <span class="text-2xl font-bold text-blue-900">{{ strtoupper(substr($rec?->name ?? 'R', 0, 1)) }}</span>
                                                    </div>
                                                @endif

                                                <div class="flex-1">
                                                    <h3 class="text-lg font-semibold text-blue-900">{{ $rec?->name ?? 'Unknown Recruiter' }}</h3>
                                                    <p class="text-sm text-blue-700">{{ $rec?->location ?? 'Location not set' }}</p>
                                                    <p class="text-sm text-blue-600 mt-1">{{ $rec?->bio ?? '' }}</p>
                                                    <div class="mt-2 text-sm text-blue-600">Rating: <span class="font-medium">{{ $rec?->avg_rating ?? '0.00' }}</span> ({{ $rec?->rating_count ?? 0 }})</div>
                                                </div>
                                            </div>

                                            <hr class="my-4">

                                            <div class="text-sm text-blue-800">
                                                <h4 class="font-semibold text-blue-900">{{ $job->title }}</h4>
                                                <p class="text-blue-600 mt-2">{{ $job->description }}</p>

                                                <div class="mt-4 grid grid-cols-1 md:grid-cols-4 gap-3 text-sm">
                                                    <div><span class="font-semibold">Difficulty:</span> <span class="text-blue-700">{{ ucfirst($job->difficulty) }}</span></div>
                                                    <div><span class="font-semibold">Timing:</span> <span class="text-blue-700">{{ $job->working_hours }} hrs/day</span></div>
                                                    <div><span class="font-semibold">Location:</span> <span class="text-blue-700">{{ $job->location ?? 'Not Given' }}</span></div>
                                                    <div><span class="font-semibold">Payment:</span> <span class="text-blue-700">{{ number_format($job->payment,2) }} taka/hr</span></div>
                                                </div>
                                            </div>

                                            <div class="mt-6 flex gap-3">
                                                <button @click="open = false" type="button" class="bg-white border border-blue-200 text-blue-700 px-4 py-2 rounded text-sm">Close</button>

                                                <form action="{{ route('jobs.apply', $job->id) }}" method="POST">
                                                    @csrf
                                                    <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded text-sm">Apply</button>
                                                </form>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="bg-white rounded-lg p-8 text-center border border-blue-200">
                        <div class="mx-auto h-12 w-12 rounded-full bg-blue-100 flex items-center justify-center mb-3">
                            <svg class="h-6 w-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                    d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                            </svg>
                        </div>
                        <p class="text-blue-600 font-medium">No jobs available at the moment</p>
                        <p class="text-blue-500 text-sm mt-1">Check back later for new opportunities!</p>
                    </div>
                @endforelse
            </div>
        </div>
    </div>
</x-layouts.app>