<x-layouts.app :title="__('Ratings & Reviews')">
    <div class="min-h-screen bg-gradient-to-br from-blue-100 via-blue-50 to-blue-200 py-12">
        <div class="max-w-5xl mx-auto px-4">

            @if(session('success'))
                <div class="mb-4 bg-emerald-50 border border-emerald-200 text-emerald-700 px-4 py-3 rounded-xl">
                    {{ session('success') }}
                </div>
            @endif
            @if(session('error'))
                <div class="mb-4 bg-rose-50 border border-rose-200 text-rose-700 px-4 py-3 rounded-xl">
                    {{ session('error') }}
                </div>
            @endif

            <div class="mb-8">
                <h1 class="text-3xl font-bold text-blue-900 tracking-tight">Ratings Center</h1>
                <p class="text-blue-700">Manage your reputation and provide feedback to the community.</p>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">

                <div class="lg:col-span-1 space-y-6">
                    <div class="bg-white rounded-2xl p-6 border border-blue-200 shadow-sm">
                        <h4 class="text-[11px] font-bold text-blue-400 uppercase tracking-widest mb-4">Your Reputation</h4>
                        <div class="flex flex-col items-center py-4">
                            <div class="text-5xl font-black text-blue-900">{{ number_format($averageRating, 1) }}</div>
                            @php $ratingRounded = round($averageRating); @endphp
                            <div class="flex text-amber-400 my-2">
                                @for($i = 1; $i <= 5; $i++)
                                    <svg class="w-5 h-5 {{ $i <= $ratingRounded ? 'fill-current' : 'text-slate-200' }}" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/></svg>
                                @endfor
                            </div>
                            <p class="text-xs text-blue-500 font-medium">Based on {{ $ratingCount }} reviews</p>
                        </div>

                        <div class="mt-4 pt-4 border-t border-blue-50 space-y-3">
                            <h5 class="text-[10px] font-bold text-blue-400 uppercase tracking-wider">Rating Breakdown</h5>
                            @for($i = 5; $i >= 1; $i--)
                                @php $count = $distribution[$i] ?? 0; $pct = $ratingCount ? round(($count / $ratingCount) * 100) : 0; @endphp
                                <div class="flex items-center gap-2 text-xs text-slate-600">
                                    <span class="w-6 text-right font-semibold">{{ $i }}★</span>
                                    <div class="flex-1 h-2 bg-slate-100 rounded-full overflow-hidden">
                                        <div class="h-2 bg-blue-500" style="width: {{ $pct }}%"></div>
                                    </div>
                                    <span class="w-10 text-right text-slate-500">{{ $count }}</span>
                                </div>
                            @endfor
                        </div>
                    </div>
                </div>

                <div class="lg:col-span-2 space-y-6">

                    <div class="bg-white rounded-2xl border border-blue-200 shadow-sm overflow-hidden">
                        <div class="p-5 border-b border-blue-50 bg-blue-50/30 flex justify-between items-center">
                            <h3 class="font-bold text-blue-900">Rate Your Recent Collaborations</h3>
                            <span class="bg-blue-600 text-white text-[10px] px-2 py-1 rounded-full font-bold">Action Required</span>
                        </div>
                        <div class="divide-y divide-blue-50">
                            @forelse($pendingRatings as $pending)
                                <div class="p-4 flex items-center justify-between gap-3 hover:bg-slate-50 transition-colors">
                                    <div class="flex items-center gap-3">
                                        @php
                                            $pic = $pending['other_user']?->profile_pic;
                                            if ($pic && ! str_contains($pic, '.')) { $pic = $pic . '.png'; }
                                        @endphp
                                        @if($pic && file_exists(public_path('avatars/' . $pic)))
                                            <img src="{{ asset('avatars/' . $pic) }}" class="h-10 w-10 rounded-full object-cover" alt="avatar" />
                                        @else
                                            <div class="h-10 w-10 rounded-full bg-blue-100 flex items-center justify-center font-bold text-blue-700">{{ strtoupper(substr($pending['other_user']?->name ?? 'U',0,1)) }}</div>
                                        @endif
                                        <div>
                                            <p class="text-sm font-bold text-slate-900">{{ $pending['other_user']?->name ?? 'User' }}</p>
                                            <p class="text-xs text-slate-500">Project: {{ $pending['job_title'] }}</p>
                                        </div>
                                    </div>
                                    <form action="{{ route('ratings.submit') }}" method="POST" class="flex items-center gap-2">
                                        @csrf
                                        <input type="hidden" name="rated_user_id" value="{{ $pending['other_user']->id ?? '' }}">
                                        <input type="hidden" name="job_id" value="{{ $pending['job_id'] }}">
                                        <select name="rating" class="text-sm border border-slate-200 rounded-lg px-2 py-1 focus:ring-blue-500 focus:border-blue-500">
                                            @for($i=5;$i>=1;$i--)
                                                <option value="{{ $i }}">{{ $i }} ★</option>
                                            @endfor
                                        </select>
                                        <button type="submit" class="text-xs bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded-lg transition-all active:scale-95">Submit</button>
                                    </form>
                                </div>
                            @empty
                                <div class="p-8 text-center text-slate-400 text-sm">
                                    All caught up! No pending reviews to submit.
                                </div>
                            @endforelse
                        </div>
                    </div>

                    <div class="bg-white rounded-2xl border border-blue-200 shadow-sm overflow-hidden">
                        <div class="p-5 border-b border-blue-50">
                            <h3 class="font-bold text-blue-900">Reviews About You</h3>
                        </div>
                        <div class="divide-y divide-slate-50">
                            @forelse($receivedRatings as $rating)
                                <div class="p-4 flex items-start gap-3">
                                    @php
                                        $rPic = $rating->rater->profile_pic ?? null;
                                        if ($rPic && ! str_contains($rPic, '.')) { $rPic = $rPic . '.png'; }
                                    @endphp
                                    @if($rPic && file_exists(public_path('avatars/' . $rPic)))
                                        <img src="{{ asset('avatars/' . $rPic) }}" class="h-10 w-10 rounded-full object-cover" alt="avatar" />
                                    @else
                                        <div class="h-10 w-10 rounded-full bg-blue-100 flex items-center justify-center font-bold text-blue-700">{{ strtoupper(substr($rating->rater->name ?? 'U',0,1)) }}</div>
                                    @endif
                                    <div class="flex-1">
                                        <div class="flex items-center justify-between">
                                            <div>
                                                <p class="text-sm font-bold text-slate-900">{{ $rating->rater->name ?? 'User' }}</p>
                                                <p class="text-xs text-slate-500">{{ $rating->job->title ?? 'Job' }}</p>
                                            </div>
                                            <div class="flex items-center text-amber-400 text-xs font-bold">
                                                <svg class="w-4 h-4 fill-current" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/></svg>
                                                <span class="ml-1">{{ $rating->rating }}</span>
                                            </div>
                                        </div>
                                        @if($rating->review)
                                            <p class="text-sm text-slate-700 mt-2">{{ $rating->review }}</p>
                                        @endif
                                        <p class="text-[11px] text-slate-400 mt-1">{{ $rating->created_at->diffForHumans() }}</p>
                                    </div>
                                </div>
                            @empty
                                <div class="p-6 text-center text-slate-400 text-sm">No one has reviewed you yet.</div>
                            @endforelse
                        </div>
                    </div>

                    <div class="bg-white rounded-2xl border border-blue-200 shadow-sm overflow-hidden">
                        <div class="p-5 border-b border-blue-50">
                            <h3 class="font-bold text-blue-900">Your Review History</h3>
                        </div>
                        <div class="divide-y divide-slate-50">
                            @forelse($givenRatings as $rating)
                                <div class="p-4 flex items-start gap-3">
                                    @php
                                        $gPic = $rating->ratedUser->profile_pic ?? null;
                                        if ($gPic && ! str_contains($gPic, '.')) { $gPic = $gPic . '.png'; }
                                    @endphp
                                    @if($gPic && file_exists(public_path('avatars/' . $gPic)))
                                        <img src="{{ asset('avatars/' . $gPic) }}" class="h-10 w-10 rounded-full object-cover" alt="avatar" />
                                    @else
                                        <div class="h-10 w-10 rounded-full bg-blue-100 flex items-center justify-center font-bold text-blue-700">{{ strtoupper(substr($rating->ratedUser->name ?? 'U',0,1)) }}</div>
                                    @endif
                                    <div class="flex-1">
                                        <div class="flex items-center justify-between">
                                            <div>
                                                <p class="text-sm font-bold text-slate-900">{{ $rating->ratedUser->name ?? 'User' }}</p>
                                                <p class="text-xs text-slate-500">{{ $rating->job->title ?? 'Job' }}</p>
                                            </div>
                                            <div class="flex items-center text-amber-400 text-xs font-bold">
                                                <svg class="w-4 h-4 fill-current" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/></svg>
                                                <span class="ml-1">{{ $rating->rating }}</span>
                                            </div>
                                        </div>
                                        @if($rating->review)
                                            <p class="text-sm text-slate-700 mt-2">{{ $rating->review }}</p>
                                        @endif
                                        <p class="text-[11px] text-slate-400 mt-1">{{ $rating->created_at->diffForHumans() }}</p>
                                    </div>
                                </div>
                            @empty
                                <div class="p-6 text-center text-slate-400 text-sm">You have not submitted any reviews yet.</div>
                            @endforelse
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </div>
</x-layouts.app>