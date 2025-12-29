<x-layouts.app :title="__('Recruiter Dashboard')">
    <div class="min-h-screen bg-gradient-to-br from-blue-100 via-blue-50 to-blue-200">
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
            <h3 class="text-lg font-bold text-blue-900 mb-4">Job Applicants</h3>

            @if(session('success'))
                <div class="bg-emerald-50 border border-emerald-200 text-emerald-700 px-4 py-3 rounded-xl mb-4">
                    {{ session('success') }}
                </div>
            @endif
            @if(session('error'))
                <div class="bg-rose-50 border border-rose-200 text-rose-700 px-4 py-3 rounded-xl mb-4">
                    {{ session('error') }}
                </div>
            @endif

            <div class="space-y-4">
                @forelse($applicants as $applicant)
                    @php
                        $status = $applicant->STATUS ?? $applicant->status ?? 'pending';
                        $seeker = $applicant->seeker ?? null;
                        $job = $applicant->job ?? null;
                    @endphp
                    <div class="bg-white rounded-lg p-4 border border-blue-200">
                        <div class="flex justify-between items-start">
                            <div class="flex-1">
                                <div class="flex items-center gap-3">
                                    @php
                                        $seekerPic = $seeker?->profile_pic ?? null;
                                        if ($seekerPic && ! str_contains($seekerPic, '.')) {
                                            $seekerPic = $seekerPic . '.png';
                                        }
                                    @endphp
                                    @if($seekerPic && file_exists(public_path('avatars/'.$seekerPic)))
                                        <img src="{{ asset('avatars/'.$seekerPic) }}" alt="{{ $seeker?->name ?? 'User' }}" class="h-12 w-12 rounded-full object-cover" />
                                    @else
                                        <div class="h-12 w-12 rounded-full bg-blue-200 flex items-center justify-center">
                                            <span class="text-lg font-bold text-blue-900">{{ strtoupper(substr($seeker?->name ?? 'U', 0, 1)) }}</span>
                                        </div>
                                    @endif
                                    <div>
                                        <h4 class="font-semibold text-blue-900">{{ $seeker?->name ?? 'Unknown User' }}</h4>
                                        <p class="text-blue-700 text-sm">{{ $seeker?->email ?? 'N/A' }}</p>
                                    </div>
                                </div>
                                
                                <div class="mt-3 flex items-center gap-4 text-sm">
                                    <span class="text-blue-600">Applied for: <span class="font-medium">{{ $job?->title ?? 'Job' }}</span></span>
                                    <span class="text-blue-500">{{ \Carbon\Carbon::parse($applicant->applied_at ?? $applicant->created_at)->diffForHumans() }}</span>
                                </div>
                                
                                <div class="mt-2">
                                    <span class="px-3 py-1 text-xs rounded-full 
                                        {{ $status === 'pending' ? 'bg-yellow-100 text-yellow-800' : '' }}
                                        {{ $status === 'accepted' ? 'bg-green-100 text-green-800' : '' }}
                                        {{ $status === 'rejected' ? 'bg-red-100 text-red-800' : '' }}">
                                        {{ ucfirst($status) }}
                                    </span>
                                </div>
                            </div>
                            
                            <div x-data="{ submitting: false, csrf: '{{ csrf_token() }}', submitStatus: async function(url, status) { if (this.submitting) return; this.submitting = true; try { const fd = new FormData(); fd.append('_token', this.csrf); fd.append('status', status); const res = await fetch(url, { method: 'POST', body: fd, credentials: 'same-origin' }); if (!res.ok) throw new Error('Network error'); const ct = res.headers.get('content-type') || ''; if (ct.includes('application/json')) await res.json(); location.reload(); } catch (e) { console.error(e); alert('Failed to update status.'); } finally { this.submitting = false; } } }" class="flex gap-2">
                                @if($status === 'pending')
                                    <button @click.prevent="submitStatus('{{ route('applications.update_status', $applicant->id) }}', 'accepted')" :disabled="submitting" class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded text-sm">
                                        Accept
                                    </button>

                                    <button @click.prevent="submitStatus('{{ route('applications.update_status', $applicant->id) }}', 'rejected')" :disabled="submitting" class="bg-red-600 hover:bg-red-700 text-white px-4 py-2 rounded text-sm">
                                        Reject
                                    </button>

                                @else
                                    <div class="flex items-center">
                                        <span class="text-sm text-gray-700 px-3 py-1 rounded bg-gray-100">Application already processed!</span>
                                    </div>

                                @endif
                                <div x-data="{
                                        open: false,
                                        submitting: false,
                                        csrf: '{{ csrf_token() }}',
                                        url: '{{ route('applications.update_status', $applicant->id) }}',
                                        async submitStatus(status) {
                                            if (this.submitting) return;
                                            this.submitting = true;
                                            try {
                                                const fd = new FormData();
                                                fd.append('_token', this.csrf);
                                                fd.append('status', status);
                                                const res = await fetch(this.url, { method: 'POST', body: fd, credentials: 'same-origin' });
                                                if (!res.ok) throw new Error('Network error');
                                                await res.json();
                                                this.open = false;
                                                location.reload();
                                            } catch (e) {
                                                console.error(e);
                                                alert('Failed to update status.');
                                            } finally {
                                                this.submitting = false;
                                            }
                                        }
                                    }" class="relative">
                                    <button @click="open = true" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded text-sm">
                                        View Application
                                    </button>

                                    <!-- Modal -->
                                    <div x-show="open" x-cloak class="fixed inset-0 z-50 flex items-center justify-center">
                                        <div class="fixed inset-0 bg-black/40" @click="open = false"></div>

                                        <div class="bg-white rounded-lg max-w-2xl w-full mx-4 z-50 overflow-auto" @keydown.window.escape="open = false">
                                            <div class="p-6">
                                                <div class="flex items-start gap-4">
                                                    @php
                                                        $modalSeeker = $seeker;
                                                        $modalSeekerPic = $seeker?->profile_pic ?? null;
                                                        if ($modalSeekerPic && ! str_contains($modalSeekerPic, '.')) {
                                                            $modalSeekerPic = $modalSeekerPic . '.png';
                                                        }
                                                    @endphp
                                                    @if($modalSeekerPic && file_exists(public_path('avatars/'.$modalSeekerPic)))
                                                        <img src="{{ asset('avatars/'.$modalSeekerPic) }}" alt="{{ $seeker?->name ?? 'User' }}" class="h-24 w-24 rounded-full object-cover" />
                                                    @else
                                                        <div class="h-24 w-24 rounded-full bg-blue-200 flex items-center justify-center">
                                                            <span class="text-2xl font-bold text-blue-900">{{ strtoupper(substr($seeker?->name ?? 'U', 0, 1)) }}</span>
                                                        </div>
                                                    @endif

                                                    <div class="flex-1">
                                                        <h3 class="text-xl font-semibold text-blue-900">{{ $seeker?->name ?? 'Unknown' }}</h3>
                                                        <p class="text-sm text-blue-700">{{ $seeker?->email ?? 'N/A' }}</p>
                                                        <div class="mt-2 text-sm text-blue-600">Applied for: <span class="font-medium text-blue-800">{{ $job?->title ?? 'Job' }}</span></div>
                                                        <div class="mt-1 text-sm text-blue-500">Status: <span class="font-medium">{{ ucfirst($status) }}</span></div>
                                                        <div class="mt-1 text-sm text-blue-500">Applied {{ \Carbon\Carbon::parse($applicant->applied_at ?? $applicant->created_at ?? now())->diffForHumans() }}</div>
                                                    </div>
                                                </div>

                                                <hr class="my-4">

                                                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-sm text-blue-800">
                                                    <div>
                                                        <p class="font-semibold">Bio</p>
                                                        <p class="text-blue-600">{{ $seeker?->bio ?? 'No bio available.' }}</p>
                                                    </div>
                                                    <div>
                                                        <p class="font-semibold">Location</p>
                                                        <p class="text-blue-600">{{ $seeker?->location ?? 'N/A' }}</p>
                                                    </div>
                                                    <div>
                                                        <p class="font-semibold">Skills</p>
                                                        @php
                                                            $rawSkills = $seeker?->skills ?? '';
                                                            $skills = collect(array_filter(array_map('trim', explode(',', $rawSkills))))->values();
                                                        @endphp
                                                        @if($skills->isEmpty())
                                                            <p class="text-blue-600">N/A</p>
                                                        @else
                                                            <div class="flex flex-wrap gap-2">
                                                                @foreach($skills as $skill)
                                                                    <span class="text-xs bg-blue-50 text-blue-700 px-2 py-1 rounded">{{ $skill }}</span>
                                                                @endforeach
                                                            </div>
                                                        @endif
                                                    </div>
                                                    <div>
                                                        <p class="font-semibold">Ratings</p>
                                                        <p class="text-blue-600">{{ $seeker?->avg_rating ?? '0.00' }} ({{ $seeker?->rating_count ?? 0 }} reviews)</p>
                                                    </div>
                                                </div>

                                                <div class="mt-6 flex gap-3">
                                                    <a href="{{ route('inbox.start-chat', $seeker?->id ?? 0) }}" class="bg-gray-200 hover:bg-gray-300 text-gray-800 px-4 py-2 rounded text-sm">Contact</a>

                                                    <button @click.prevent="submitStatus('accepted')" :disabled="submitting" class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded text-sm">Accept</button>

                                                    <button @click.prevent="submitStatus('rejected')" :disabled="submitting" class="bg-red-600 hover:bg-red-700 text-white px-4 py-2 rounded text-sm">Reject</button>

                                                    <button @click.prevent="submitStatus('pending')" :disabled="submitting" class="bg-yellow-100 hover:bg-yellow-200 text-yellow-800 px-4 py-2 rounded text-sm">Hold</button>

                                                    <button @click="open = false" class="ml-auto bg-white border border-blue-200 text-blue-700 px-4 py-2 rounded text-sm">Close</button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                @empty
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
                @endforelse
            </div>
        </div>
    </div>
</x-layouts.app>