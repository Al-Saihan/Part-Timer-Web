<x-layouts.app :title="__('Posted Jobs')">
    <div class="min-h-screen bg-slate-50">
        <div class="max-w-7xl mx-auto px-4 py-8">
            @if(session('success'))
                <div class="bg-emerald-50 border border-emerald-200 text-emerald-700 px-4 py-3 rounded-xl mb-6 flex items-center gap-2">
                    <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path></svg>
                    {{ session('success') }}
                </div>
            @endif

            <div class="flex justify-between items-end mb-8">
                <div>
                    <h2 class="text-2xl font-bold text-slate-900">Management Console</h2>
                    <p class="text-slate-500 text-sm">Review applications and manage your active listings</p>
                </div>
                <a href="{{ route('jobs.create') }}" class="bg-blue-600 hover:bg-blue-700 text-white px-5 py-2.5 rounded-xl text-sm font-bold shadow-lg shadow-blue-100 transition-all active:scale-95">
                    + Post New Job
                </a>
            </div>

            <div x-data="{ 
                open:false, selected:null, selectedApplicant:null, applicantModal:false, modalAlertHtml:'', modalAlertSuccess:true,
                selectJob(el){
                    let raw = el.getAttribute('data-job');
                    try { this.selected = JSON.parse(raw); } catch(e){ this.selected=null; }
                    if (this.selected && this.selected.applicants){
                        this.selected.applicants.forEach(a=>{ try{ if(a.profile_pic && a.profile_pic.indexOf('.')===-1) a.profile_pic = a.profile_pic + '.png'; }catch(_){} });
                    }
                    this.open = !!this.selected;
                    this.selectedApplicant = null;
                },
                selectApplicant(app){ 
                    this.modalAlertHtml = ''; 
                    this.selectedApplicant = app; 
                    this.applicantModal = true; 
                },
                close(){ this.open=false; this.selected=null; this.selectedApplicant=null; this.applicantModal=false },
                updateStatus(id, status){
                    fetch('/applications/' + id + '/status', {
                        method: 'POST',
                        headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
                        body: JSON.stringify({ status: status })
                    }).then(async res => {
                        let json = {};
                        try { json = await res.json(); } catch(_){}
                        const op = String(status).charAt(0).toUpperCase() + String(status).slice(1);
                        const opHtml = '<strong>' + op + '</strong>';
                        if (res.ok) {
                            this.modalAlertHtml = json.message ? (json.message + ' — ' + opHtml) : ('Updated to ' + opHtml);
                            this.modalAlertSuccess = true;
                            if (this.selectedApplicant && this.selectedApplicant.id == id) this.selectedApplicant.status = status;
                            if (this.selected) this.selected.applicants = this.selected.applicants.map(a => a.id == id ? Object.assign({}, a, { status: status }) : a);
                        } else {
                            this.modalAlertHtml = 'Error updating status — ' + opHtml;
                            this.modalAlertSuccess = false;
                        }
                    }).catch(err => {
                        this.modalAlertHtml = 'Network error';
                        this.modalAlertSuccess = false;
                    });
                }
            }" class="flex gap-6 items-start">

                <div class="flex-1 space-y-4">
                @forelse($jobs as $job)
                    @php
                        $appCount = $job->applications_count ?? ($job->applications->count() ?? 0);
                        $applicantsArr = [];
                        foreach($job->applications ?? [] as $app){
                            $seeker = $app->seeker ?? null;
                            $applicantsArr[] = [
                                'id' => $app->id,
                                'name' => $seeker->name ?? 'Unknown',
                                'email' => $seeker->user->email ?? 'N/A',
                                'bio' => $seeker->bio ?? '',
                                'location' => $seeker->location ?? '',
                                'skills' => $seeker->skills ?? '',
                                'avg_rating' => $seeker->avg_rating ?? 0,
                                'rating_count' => $seeker->rating_count ?? 0,
                                'profile_pic' => $seeker->profile_pic ?? null,
                                'applied_at' => optional($app->applied_at ?? $app->created_at)->toDateTimeString(),
                                'status' => $app->status ?? 'pending'
                            ];
                        }
                        $payload = ['job' => $job, 'applicants' => $applicantsArr];
                    @endphp

                    <div class="bg-white rounded-2xl p-6 border border-slate-100 shadow-sm hover:shadow-md transition-shadow">
                        <div class="flex justify-between items-start">
                            <div class="flex-1">
                                <div class="flex items-center gap-3 mb-2">
                                    <h3 class="text-lg font-bold text-slate-900 leading-tight">{{ $job->title }}</h3>
                                    <span class="px-3 py-1 bg-blue-50 text-blue-700 text-[11px] font-bold rounded-full uppercase tracking-wider">
                                        {{ $appCount }} {{ Str::plural('Applicant', $appCount) }}
                                    </span>
                                </div>
                                <p class="text-slate-500 text-sm flex items-center gap-3">
                                    <span class="capitalize font-medium text-slate-700">{{ $job->difficulty }}</span>
                                    <span>•</span>
                                    <span>{{ $job->working_hours }} hrs/week</span>
                                    <span>•</span>
                                    <span class="text-slate-700">{{ $job->location ?? 'Not Given' }}</span>
                                    <span>•</span>
                                    <span class="text-blue-600 font-bold">${{ number_format($job->payment, 2) }}</span>
                                </p>
                                <p class="text-slate-600 text-sm mt-4 leading-relaxed">{{ Str::limit($job->description, 140) }}</p>
                            </div>
                        </div>

                        <div class="flex justify-between items-center mt-6 pt-4 border-t border-slate-50">
                            <div class="text-[11px] font-bold text-slate-400 uppercase tracking-widest flex gap-3">
                                <span>Posted {{ $job->created_at->diffForHumans() }}</span>
                            </div>
                            <button data-job='{!! json_encode($payload) !!}' @click="selectJob($event.currentTarget)" 
                                class="text-blue-600 hover:text-blue-700 text-sm font-bold flex items-center gap-1 group">
                                Manage Applicants 
                                <svg class="w-4 h-4 transform group-hover:translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg>
                            </button>
                        </div>
                    </div>
                @empty
                    <div class="bg-white rounded-2xl p-12 text-center border border-dashed border-slate-300">
                        <p class="text-slate-500 font-medium">No jobs posted yet. Ready to hire?</p>
                        <a href="{{ route('jobs.create') }}" class="mt-4 inline-block text-blue-600 font-bold hover:underline">Post your first listing →</a>
                    </div>
                @endforelse
                </div>

                <div class="w-1/3 sticky top-24" x-show="open" x-cloak x-transition>
                    <div class="bg-white rounded-2xl border border-slate-200 shadow-2xl shadow-blue-100 overflow-hidden relative">
                        <template x-if="selected">
                            <div class="flex flex-col max-h-[85vh]">
                                <div class="p-5 border-b border-slate-50 bg-slate-50/50 flex justify-between items-center">
                                    <h4 class="font-bold text-slate-900 truncate pr-6" x-text="selected.job.title"></h4>
                                    <button @click="close()" class="text-slate-400 hover:text-slate-600"><svg class="h-5 w-5" fill="currentColor" viewBox="0 0 20 20"><path d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z"/></svg></button>
                                </div>

                                <div class="overflow-y-auto p-5 space-y-6">
                                    <section>
                                        <h5 class="text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-3">Applicants Queue</h5>
                                        <div class="space-y-2">
                                            <template x-for="app in selected.applicants" :key="app.id">
                                                <button @click="selectApplicant(app)" :class="selectedApplicant?.id === app.id ? 'bg-blue-50 border-blue-100' : 'hover:bg-slate-50 border-transparent'" class="w-full text-left p-3 rounded-xl border transition-all flex items-center gap-3">
                                                    <img :src="app.profile_pic ? '/avatars/' + app.profile_pic : '/avatars/default.png'" class="h-10 w-10 rounded-lg object-cover" />
                                                    <div class="flex-1 min-w-0">
                                                        <div class="flex items-center justify-between">
                                                            <div class="text-sm font-bold text-slate-900 truncate" x-text="app.name"></div>
                                                            <span class="text-[9px] px-1.5 py-0.5 rounded font-bold uppercase border" 
                                                                  :class="{
                                                                    'bg-amber-50 text-amber-600 border-amber-100': app.status === 'pending',
                                                                    'bg-emerald-50 text-emerald-600 border-emerald-100': app.status === 'accepted',
                                                                    'bg-rose-50 text-rose-600 border-rose-100': app.status === 'rejected'
                                                                  }" x-text="app.status"></span>
                                                        </div>
                                                        <div class="text-[10px] text-slate-400" x-text="new Date(app.applied_at).toLocaleDateString()"></div>
                                                    </div>
                                                </button>
                                            </template>
                                        </div>
                                    </section>
                                    <div class="pt-4 text-xs text-slate-400 italic">Click an applicant to view full details and take action.</div>
                                </div>
                            </div>
                        </template>
                    </div>
                </div>

                <div x-show="applicantModal" x-cloak @keydown.window.escape="applicantModal=false; selectedApplicant=null" x-transition class="fixed inset-0 z-50 flex items-center justify-center px-4">
                    <div class="absolute inset-0 bg-black/45" @click="applicantModal=false; selectedApplicant=null"></div>
                    
                    <div x-show="applicantModal" 
                         x-transition:enter="transform transition ease-out duration-200" 
                         x-transition:enter-start="opacity-0 scale-95" 
                         x-transition:enter-end="opacity-100 scale-100" 
                         class="bg-white rounded-2xl shadow-2xl z-10 w-full max-w-2xl mx-auto overflow-hidden">
                        
                        <div class="p-8" x-if="selectedApplicant">
                            <div class="flex items-start gap-6">
                                <template x-if="selectedApplicant && selectedApplicant.profile_pic">
                                    <img :src="'/avatars/' + selectedApplicant.profile_pic" class="h-24 w-24 rounded-full object-cover shadow-md border-2 border-blue-50" />
                                </template>
                                <template x-if="!selectedApplicant || !selectedApplicant.profile_pic">
                                    <div class="h-24 w-24 rounded-full bg-blue-100 flex items-center justify-center shadow-inner border-2 border-blue-50">
                                        <span class="text-3xl font-bold text-blue-900" x-text="selectedApplicant ? selectedApplicant.name.charAt(0).toUpperCase() : 'U'"></span>
                                    </div>
                                </template>

                                <div class="flex-1">
                                    <div class="flex justify-between items-start">
                                        <div>
                                            <h3 class="text-2xl font-bold text-blue-900" x-text="selectedApplicant.name"></h3>
                                            <p class="text-blue-700 font-medium" x-text="selectedApplicant.email"></p>
                                        </div>
                                        <button @click="applicantModal=false; selectedApplicant=null" class="text-slate-400 hover:text-slate-600">
                                            <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                                        </button>
                                    </div>
                                    <div class="mt-3 flex flex-col gap-1">
                                        <div class="text-sm text-blue-600">Applied for: <span class="font-bold text-blue-800" x-text="selected.job.title"></span></div>
                                        <div class="text-sm text-blue-500 font-medium">Status: <span class="capitalize" x-text="selectedApplicant.status"></span></div>
                                        <div class="text-sm text-slate-400">Applied <span x-text="new Date(selectedApplicant.applied_at).toLocaleDateString()"></span></div>
                                    </div>
                                </div>
                            </div>

                            <hr class="my-6 border-slate-100">

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 text-sm">
                                <div>
                                    <p class="font-bold text-blue-900 uppercase tracking-tight mb-2">Bio</p>
                                    <p class="text-slate-600 leading-relaxed" x-text="selectedApplicant.bio || 'No bio provided.'"></p>
                                </div>
                                <div>
                                    <p class="font-bold text-blue-900 uppercase tracking-tight mb-2">Location</p>
                                    <p class="text-slate-600" x-text="selectedApplicant.location || 'Not specified'"></p>
                                </div>
                                <div>
                                    <p class="font-bold text-blue-900 uppercase tracking-tight mb-2">Skills</p>
                                    <div class="flex flex-wrap gap-2">
                                        <template x-for="(skill, idx) in (selectedApplicant.skills ? selectedApplicant.skills.split(',').map(s=>s.trim()).filter(Boolean) : [])" :key="idx">
                                            <span class="px-2 py-1 bg-blue-50 text-blue-700 rounded-md text-[11px] font-bold border border-blue-100" x-text="skill"></span>
                                        </template>
                                        <template x-if="!selectedApplicant.skills">
                                            <span class="text-slate-400 italic">None listed</span>
                                        </template>
                                    </div>
                                </div>
                                <div>
                                    <p class="font-bold text-blue-900 uppercase tracking-tight mb-2">Ratings</p>
                                    <div class="flex items-center gap-2">
                                        <span class="text-lg font-bold text-blue-600" x-text="selectedApplicant.avg_rating"></span>
                                        <span class="text-slate-400" x-text="'(' + selectedApplicant.rating_count + ' reviews)'"></span>
                                    </div>
                                </div>
                            </div>

                            <div x-show="modalAlertHtml" x-cloak class="mt-6">
                                <div :class="modalAlertSuccess ? 'bg-emerald-50 text-emerald-700 border-emerald-200' : 'bg-rose-50 text-rose-700 border-rose-200'" class="border px-4 py-3 rounded-xl text-sm font-medium" x-html="modalAlertHtml"></div>
                            </div>

                            <div class="mt-8 flex gap-3">
                                <a :href="'mailto:' + selectedApplicant.email" class="bg-slate-100 hover:bg-slate-200 text-slate-700 px-6 py-2.5 rounded-xl text-sm font-bold transition-colors">Contact</a>
                                
                                <button @click="updateStatus(selectedApplicant.id, 'accepted')" class="flex-1 bg-emerald-600 hover:bg-emerald-700 text-white py-2.5 rounded-xl text-sm font-bold transition-all shadow-lg shadow-emerald-100">Accept</button>
                                
                                <button @click="updateStatus(selectedApplicant.id, 'rejected')" class="flex-1 bg-rose-50 hover:bg-rose-100 text-rose-600 py-2.5 rounded-xl text-sm font-bold transition-colors">Reject</button>
                                
                                <button @click="updateStatus(selectedApplicant.id, 'pending')" class="px-6 bg-amber-50 hover:bg-amber-100 text-amber-700 py-2.5 rounded-xl text-sm font-bold transition-colors">Hold</button>
                            </div>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>
</x-layouts.app>