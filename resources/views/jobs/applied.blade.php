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
            <div x-data="{ open:false, selected:null, selectJob(el){
                        let raw = el.getAttribute('data-job');
                        try {
                            this.selected = JSON.parse(raw);
                        } catch(e) {
                            try {
                                this.selected = JSON.parse(decodeURIComponent(raw));
                            } catch(e2) {
                                console.error('Failed to parse job payload', e2, raw);
                                this.selected = null;
                            }
                        }
                        // normalize recruiter profile pic filename
                        if (this.selected && this.selected.recruiter && this.selected.recruiter.profile_pic) {
                            try {
                                let p = this.selected.recruiter.profile_pic;
                                if (p && p.indexOf('.') === -1) this.selected.recruiter.profile_pic = p + '.png';
                            } catch(_) {}
                        }
                        // expose for debugging
                        try { window._selected = this.selected; } catch(_) {}
                        this.open = !!this.selected;
                    }, close(){ this.open=false; this.selected=null; } }">
            <div class="flex items-center justify-between mb-6">
                <h2 class="text-2xl font-bold text-blue-900">Jobs You've Applied To</h2>
            </div>

            <div class="space-y-4 flex gap-6">
                <div class="flex-1 space-y-4">
                @forelse($applications as $application)
                    @php
                        $job = $application->job ?? null;
                        $status = $application->STATUS ?? $application->status ?? 'pending';
                        // check if chat room exists between seeker (auth user) and recruiter for this job
                        $chatRoom = null;
                        try {
                            // Check for any chat room that includes both the seeker (auth user)
                            // and the recruiter, regardless of job association.
                            $chatRoom = \App\Models\ChatRoom::whereHas('participants', function($q){
                                    $q->where('user_id', auth()->id());
                                })
                                ->whereHas('participants', function($q) use ($job){
                                    $q->where('user_id', $job->recruiter_id);
                                })
                                ->first();
                        } catch (\Throwable $e) {
                            $chatRoom = null;
                        }
                        $payload = [
                            'job' => [
                                'id' => $job->id ?? null,
                                'title' => $job->title ?? '',
                                'description' => $job->description ?? '',
                                'difficulty' => $job->difficulty ?? '',
                                'working_hours' => $job->working_hours ?? '',
                                'payment' => $job->payment ?? '',
                                'created_at' => optional($job->created_at)->toDateTimeString(),
                            ],
                            'recruiter' => [
                                'id' => $job->recruiter->id ?? $job->recruiter_id ?? null,
                                'name' => $job->recruiter->name ?? '',
                                'email' => $job->recruiter->email ?? '',
                                'bio' => $job->recruiter->bio ?? '',
                                'location' => $job->recruiter->location ?? '',
                                'avg_rating' => $job->recruiter->avg_rating ?? 0,
                                'rating_count' => $job->recruiter->rating_count ?? 0,
                                'profile_pic' => $job->recruiter->profile_pic ?? null,
                            ],
                            'application' => [
                                'id' => $application->id ?? null,
                                'status' => $status,
                                'applied_at' => optional($application->applied_at ?? $application->created_at)->toDateTimeString(),
                            ],
                            'chat' => [ 'exists' => (bool) $chatRoom, 'id' => $chatRoom->id ?? null ]
                        ];
                    @endphp
                    <div class="bg-white rounded-xl p-5 border border-slate-100 shadow-sm hover:shadow-md transition-shadow duration-200">
                        <div class="flex flex-col md:flex-row md:justify-between gap-4">
                            <div class="flex-1">
                                <div class="flex items-center gap-3 mb-2">
                                    <h3 class="text-lg font-bold text-slate-900 leading-tight">{{ $job?->title ?? 'Untitled Position' }}</h3>
                                    <span class="px-2.5 py-0.5 text-xs font-medium rounded-full capitalize
                                        {{ $status === 'pending' ? 'bg-amber-50 text-amber-700 border border-amber-100' : '' }}
                                        {{ $status === 'accepted' ? 'bg-emerald-50 text-emerald-700 border border-emerald-100' : '' }}
                                        {{ $status === 'rejected' ? 'bg-rose-50 text-rose-700 border border-rose-100' : '' }}">
                                        {{ $status }}
                                    </span>
                                </div>

                                <div class="flex flex-wrap items-center gap-y-2 gap-x-4 text-sm text-slate-500 mb-4">
                                    <div class="flex items-center gap-1">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
                                        <span class="capitalize">{{ $job?->difficulty ?? 'n/a' }}</span>
                                    </div>
                                    <div class="flex items-center gap-1">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                        <span>{{ $job?->working_hours ?? '0' }} hrs/week</span>
                                    </div>
                                    <div class="flex items-center gap-1 font-semibold text-slate-700">
                                        <span>${{ isset($job->payment) ? number_format($job->payment, 2) : '0.00' }}</span>
                                    </div>
                                </div>

                                @if(!empty($job?->description))
                                    <p class="text-slate-600 text-sm leading-relaxed line-clamp-2 mb-4">
                                        {{ $job->description }}
                                    </p>
                                @endif

                                <div class="flex items-center justify-between pt-4 border-t border-slate-50">
                                    <div class="text-[11px] uppercase tracking-wider font-semibold text-slate-400 flex gap-3">
                                        <span>Applied {{ \Carbon\Carbon::parse($application->applied_at ?? $application->created_at)->diffForHumans() }}</span>
                                    </div>
                                    <button data-job='{!! json_encode($payload) !!}' @click="selectJob($event.currentTarget)" 
                                        class="group flex items-center gap-1 text-sm font-semibold text-blue-600 hover:text-blue-700 transition-colors">
                                        View Details 
                                        <svg class="w-4 h-4 transform group-hover:translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                                    </button>
                                </div>
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

                <!-- Right detail panel -->
                <div class="w-1/3 hidden md:block" x-show="open" x-cloak x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 transform translate-x-4" x-transition:enter-end="opacity-100 transform translate-x-0">
                    <div class="bg-white rounded-lg p-6 border border-blue-200 sticky top-6 relative">
                        <template x-if="selected">
                            <div class="space-y-4">
                                <!-- Close icon (top-right) -->
                                <button @click="close()" class="absolute top-3 right-3 text-slate-500 hover:text-slate-800" aria-label="Close panel">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                                        <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd" />
                                    </svg>
                                </button>

                                <div class="flex items-center gap-4">
                                    <template x-if="selected.recruiter.profile_pic">
                                        <img :src="selected.recruiter.profile_pic.includes('.') ? '/avatars/' + selected.recruiter.profile_pic : '/avatars/' + (selected.recruiter.profile_pic + '.png')" alt="" class="h-16 w-16 rounded-full object-cover" />
                                    </template>
                                    <div>
                                        <h4 class="font-semibold text-blue-900" x-text="selected.recruiter.name"></h4>
                                        <p class="text-sm text-blue-600" x-text="selected.recruiter.location"></p>
                                        <div class="text-sm text-blue-600">Rating: <span class="font-medium" x-text="selected.recruiter.avg_rating"></span> (<span x-text="selected.recruiter.rating_count"></span>)</div>
                                    </div>
                                </div>

                                <div>
                                    <p class="font-semibold">Recruiter Bio</p>
                                    <p class="text-blue-600 text-sm" x-text="selected.recruiter.bio"></p>
                                </div>

                                <hr>

                                <div>
                                    <h5 class="font-semibold text-blue-900" x-text="selected.job.title"></h5>
                                    <p class="text-blue-600 text-sm mt-2" x-text="selected.job.description"></p>
                                    <div class="mt-3 text-sm text-blue-700">
                                        <div>Posted: <span x-text="new Date(selected.job.created_at).toLocaleString()"></span></div>
                                        <div>Difficulty: <span x-text="selected.job.difficulty"></span></div>
                                        <div>Timing: <span x-text="selected.job.working_hours + ' hrs/day'"></span></div>
                                        <div>Payment: <span x-text="selected.job.payment + ' taka/hr'"></span></div>
                                    </div>
                                </div>

                                <div class="text-xs">
                                    <template x-if="selected.chat.exists">
                                        <div class="text-green-600 font-medium">The recruiter has contacted you! Check your inbox!</div>
                                    </template>
                                    <template x-if="!selected.chat.exists">
                                        <div class="text-gray-600">The recruiter hasn't yet contacted you</div>
                                    </template>
                                </div>
                            </div>
                        </template>
                    </div>
                </div>

            </div>
        </div>
    </div>
</x-layouts.app>
