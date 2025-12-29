<x-layouts.app :title="__('Profile')">
    <div class="min-h-screen bg-gradient-to-br from-blue-100 via-blue-50 to-blue-200 py-12">
        <div class="max-w-4xl mx-auto px-4">
            @php
                $userPic = $user->profile_pic ?? null;
                if ($userPic && ! str_contains($userPic, '.')) { $userPic = $userPic . '.png'; }
            @endphp
            <div x-data="{ avatarModal: false, selectedAvatar: '{{ $userPic }}' }" class="bg-white rounded-3xl shadow-sm border border-slate-100 overflow-hidden mb-6 p-6">
                <div class="flex items-center justify-between gap-6">
                    <div class="flex items-center gap-4">
                        <div class="p-1 bg-white rounded-full shadow-lg relative group cursor-pointer" @click="avatarModal = true">
                            @if($userPic && file_exists(public_path('avatars/' . $userPic)))
                                <img src="{{ asset('avatars/' . $userPic) }}" alt="Profile" class="w-28 h-28 rounded-full object-cover border-4 border-white">
                            @elseif($user->profile_pic)
                                <img src="{{ $user->profile_pic }}" alt="Profile" class="w-28 h-28 rounded-full object-cover border-4 border-white">
                            @else
                                <div class="w-28 h-28 rounded-full bg-blue-100 flex items-center justify-center text-3xl font-bold text-blue-700 border-4 border-white">
                                    {{ $user->initials() }}
                                </div>
                            @endif
                            <div class="absolute inset-0 rounded-full bg-black/45 opacity-0 group-hover:opacity-100 transition flex items-center justify-center text-white font-semibold text-sm">
                                Change Avatar
                            </div>
                        </div>
                        <div>
                            <h2 class="text-2xl font-bold text-slate-900 tracking-tight">{{ $user->name }}</h2>
                            <div class="flex items-center gap-3 text-slate-500 mt-1">
                                <span class="flex items-center gap-1 text-sm">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                                    {{ $user->email }}
                                </span>
                                <span class="text-slate-300">•</span>
                                <span class="px-3 py-1 bg-blue-50 text-blue-700 text-[11px] font-bold rounded-full uppercase tracking-wider">
                                    {{ $user->user_type }}
                                </span>
                            </div>
                        </div>
                    </div>
                </div>

                <div x-show="avatarModal" x-cloak class="fixed inset-0 z-50 flex items-center justify-center">
                    <div class="fixed inset-0 bg-black/40" @click="avatarModal = false"></div>
                    <div class="bg-white rounded-2xl shadow-xl max-w-2xl w-full mx-4 relative z-50 p-6">
                        <div class="flex items-center justify-between">
                            <div>
                                <h3 class="text-lg font-semibold text-slate-900">Choose an Avatar</h3>
                                <p class="text-sm text-slate-500">Pick one from the available set. Hover and click to select.</p>
                            </div>
                            <button type="button" @click="avatarModal = false" class="text-slate-400 hover:text-slate-600">
                                <span class="sr-only">Close</span>
                                &times;
                            </button>
                        </div>

                        <div class="grid grid-cols-3 sm:grid-cols-4 md:grid-cols-5 gap-3 mt-4 max-h-80 overflow-y-auto">
                            @forelse($availableAvatars as $avatar)
                                <button type="button" @click="selectedAvatar = '{{ $avatar }}'" class="relative border rounded-xl p-2 hover:border-blue-400 transition shadow-sm" :class="selectedAvatar === '{{ $avatar }}' ? 'ring-2 ring-blue-500 border-blue-200' : 'border-slate-200'">
                                    <img src="{{ asset('avatars/' . $avatar) }}" alt="Avatar option" class="w-full rounded-lg object-cover">
                                    <div class="absolute inset-0 rounded-xl bg-black/40 opacity-0 hover:opacity-100 flex items-center justify-center text-white text-xs font-semibold transition">
                                        Pick Avatar
                                    </div>
                                    <div x-show="selectedAvatar === '{{ $avatar }}'" class="absolute top-2 right-2 bg-blue-600 text-white text-[10px] font-bold px-2 py-1 rounded-full">Selected</div>
                                </button>
                            @empty
                                <p class="text-sm text-slate-500">No avatars found in the avatars directory.</p>
                            @endforelse
                        </div>

                        <form method="POST" action="{{ route('profile.avatar.update') }}" class="mt-6 flex justify-end gap-3">
                            @csrf
                            <input type="hidden" name="avatar" :value="selectedAvatar">
                            <button type="button" class="px-4 py-2 rounded-lg border border-slate-200 text-slate-600 hover:bg-slate-50" @click="avatarModal = false">Cancel</button>
                            <button type="submit" :disabled="!selectedAvatar" class="px-4 py-2 rounded-lg bg-blue-600 text-white font-semibold hover:bg-blue-700 disabled:opacity-60">Save Avatar</button>
                        </form>
                    </div>
                </div>
            </div>

            <div class="max-w-4xl mx-auto space-y-6">
                <!-- Activity Overview -->
                <div class="bg-white rounded-2xl p-6 border border-slate-100 shadow-sm space-y-4">
                    <h4 class="text-[11px] font-bold text-slate-400 uppercase tracking-widest">Activity Overview</h4>
                    <div class="flex items-center justify-between p-3 bg-blue-50/50 rounded-xl">
                        <span class="text-sm font-medium text-slate-600">Applied</span>
                        <span class="text-lg font-bold text-blue-700">{{ $appliedCount }}</span>
                    </div>
                    <div class="flex items-center justify-between p-3 bg-emerald-50/50 rounded-xl">
                        <span class="text-sm font-medium text-slate-600">Accepted</span>
                        <span class="text-lg font-bold text-emerald-700">{{ $acceptedCount }}</span>
                    </div>
                    @if($user->user_type === 'recruiter')
                    <div class="flex items-center justify-between p-3 bg-amber-50/50 rounded-xl">
                        <span class="text-sm font-medium text-slate-600">Posted Jobs</span>
                        <span class="text-lg font-bold text-amber-700">{{ $postedJobsCount }}</span>
                    </div>
                    @endif
                </div>

                <!-- Professional Bio -->
                <div class="bg-white rounded-2xl p-6 border border-slate-100 shadow-sm">
                    <h4 class="text-[11px] font-bold text-slate-400 uppercase tracking-widest mb-4">Professional Bio</h4>
                    <form action="{{ route('profile.bio.update') }}" method="POST">
                        @csrf
                        <textarea name="bio" rows="4" class="w-full rounded-xl border-slate-200 text-sm focus:ring-blue-500 focus:border-blue-500 px-4 py-2 bg-slate-50">{{ old('bio', $user->bio) }}</textarea>
                        @error('bio')<p class="text-xs text-red-600 mt-2">{{ $message }}</p>@enderror
                        <div class="mt-4">
                            <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-5 py-2 rounded-xl text-sm font-bold shadow-lg">Confirm Change</button>
                        </div>
                    </form>
                </div>

                <!-- Skills (seekers only) -->
                @if($user->user_type === 'seeker')
                <div class="bg-white rounded-2xl p-6 border border-slate-100 shadow-sm">
                    <h4 class="text-[11px] font-bold text-slate-400 uppercase tracking-widest mb-4">Expertise & Skills (comma separated)</h4>
                    <form action="{{ route('profile.skills.update') }}" method="POST">
                        @csrf
                        <div class="flex gap-2">
                            <input type="text" name="skills" id="skills" value="{{ old('skills', $user->skills) }}" class="flex-1 rounded-xl border-slate-200 text-sm focus:ring-blue-500 focus:border-blue-500 px-4 py-2.5 bg-slate-50" placeholder="e.g. Cooking, Cash handling, Driving">
                            <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-2 rounded-xl text-sm font-bold shadow-lg">Confirm Change</button>
                        </div>
                        @error('skills')<p class="text-xs text-red-600 mt-2">{{ $message }}</p>@enderror
                    </form>

                    <div class="pt-4 border-t border-slate-50 mt-4">
                        <div class="flex flex-wrap gap-2 mt-3">
                            @if($user->skills)
                                @foreach(explode(',', $user->skills) as $skill)
                                    <span class="px-3 py-1.5 bg-slate-100 text-slate-700 rounded-lg text-sm font-semibold border border-slate-200">{{ trim($skill) }}</span>
                                @endforeach
                            @else
                                <p class="text-slate-400 text-sm italic">No skills listed.</p>
                            @endif
                        </div>
                    </div>
                </div>
                @endif

                <!-- Location -->
                <div class="bg-white rounded-2xl p-6 border border-slate-100 shadow-sm">
                    <h4 class="text-[11px] font-bold text-slate-400 uppercase tracking-widest mb-3">Location</h4>
                    <form action="{{ route('profile.location.update') }}" method="POST">
                        @csrf
                        <div class="flex gap-2 items-center">
                            <svg class="w-5 h-5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                            <input type="text" name="location" value="{{ old('location', $user->location) }}" class="flex-1 rounded-xl border-slate-200 text-sm focus:ring-blue-500 focus:border-blue-500 px-4 py-2.5 bg-slate-50" placeholder="e.g. Dhaka, Remote">
                            <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-xl text-sm font-bold">Confirm Change</button>
                        </div>
                        @error('location')<p class="text-xs text-red-600 mt-2">{{ $message }}</p>@enderror
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-layouts.app>