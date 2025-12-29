<?php

use Illuminate\Support\Facades\Route;
use Laravel\Fortify\Features;
use Livewire\Volt\Volt;

Route::get('/', function () {
    return view('landing');
})->name('home');

// Dashboard redirect - handles initial redirect based on user type
Route::get('/dashboard', function () {
    if (auth()->user()->user_type === 'seeker') {
        return redirect()->route('seeker.dashboard');
    } elseif (auth()->user()->user_type === 'recruiter') {
        return redirect()->route('recruiter.dashboard');
    }
    return redirect('/');
})->middleware(['auth', 'verified'])->name('dashboard');

// Seeker Dashboard
Route::get('/dashboard/seeker', function () {
    $appliedJobIds = \App\Models\JobApplication::where('seeker_id', auth()->id())
        ->pluck('job_id')
        ->toArray();
    
    $jobs = \App\Models\Job::with('recruiter')
        ->whereNotIn('id', $appliedJobIds)
        ->latest()
        ->get();
    
    return view('dashboards.seeker', compact('jobs'));
})->middleware(['auth', 'verified'])->name('seeker.dashboard');

// Seeker Inbox (make-do placeholder)
Route::get('/dashboard/seeker/inbox', function () {
    return view('seeker.inbox');
})->middleware(['auth', 'verified'])->name('seeker.inbox');

// Recruiter Dashboard
Route::get('/dashboard/recruiter', function (\Illuminate\Http\Request $request) {
    $controller = new \App\Http\Controllers\Api\JobController();
    $response = $controller->getApplicants($request);
    $applicants = json_decode($response->getContent());
    
    return view('dashboards.recruiter', compact('applicants'));
})->middleware(['auth', 'verified'])->name('recruiter.dashboard');

// Update application status (recruiter action)
Route::post('/applications/{id}/status', function (\Illuminate\Http\Request $request, $id) {
    $controller = new \App\Http\Controllers\Api\JobController();
    return $controller->updateApplicationStatus($id, $request);
})->middleware(['auth', 'verified'])->name('applications.update_status');

// Posted Jobs (Recruiter)
Route::get('/jobs/posted', function (\Illuminate\Http\Request $request) {
    // Load Eloquent models so Blade can access applications and seeker relations
    $jobs = \App\Models\Job::with(['applications' => function($q){ $q->with('seeker'); }])
        ->where('recruiter_id', auth()->id())
        ->withCount('applications')
        ->latest()
        ->get();

    return view('jobs.posted', compact('jobs'));
})->middleware(['auth', 'verified'])->name('jobs.posted');

// Applied Jobs (Seeker)
Route::get('/jobs/applied', function (\Illuminate\Http\Request $request) {
    $applications = \App\Models\JobApplication::where('seeker_id', auth()->id())
        ->with(['job' => function($q) { $q->with('recruiter'); }])
        ->latest('created_at')
        ->get();

    return view('jobs.applied', compact('applications'));
})->middleware(['auth', 'verified'])->name('jobs.applied');

// Create Job Form
Route::get('/jobs/create', function () {
    return view('jobs.create');
})->middleware(['auth', 'verified'])->name('jobs.create');

// User Profile (current user)
Route::get('/profile', function (\Illuminate\Http\Request $request) {
    $user = $request->user();

    // Common data
    $appliedCount = 0;
    $acceptedCount = 0;
    $rejectedCount = 0;
    $postedJobsCount = 0;

    if ($user->user_type === 'seeker') {
        $appliedCount = \App\Models\JobApplication::where('seeker_id', $user->id)->count();
        $acceptedCount = \App\Models\JobApplication::where('seeker_id', $user->id)->where('status', 'accepted')->count();
        $rejectedCount = \App\Models\JobApplication::where('seeker_id', $user->id)->where('status', 'rejected')->count();
    } elseif ($user->user_type === 'recruiter') {
        $postedJobsCount = \App\Models\Job::where('recruiter_id', $user->id)->count();

        // Applications to this recruiter's jobs
        $applications = \App\Models\JobApplication::whereHas('job', function($q) use ($user) {
            $q->where('recruiter_id', $user->id);
        });

        $appliedCount = $applications->count();
        $acceptedCount = (clone $applications)->where('status', 'accepted')->count();
        $rejectedCount = (clone $applications)->where('status', 'rejected')->count();
    }

    $availableAvatars = collect(\Illuminate\Support\Facades\File::files(public_path('avatars')))
        ->map(fn($file) => $file->getFilename())
        ->filter(fn($name) => preg_match('/\.(png|jpe?g|gif)$/i', $name))
        ->values();

    return view('profile.show', compact('user', 'appliedCount', 'acceptedCount', 'rejectedCount', 'postedJobsCount', 'availableAvatars'));
})->middleware(['auth', 'verified'])->name('profile.show');

// Update skills (current user)
Route::post('/profile/skills', function (\Illuminate\Http\Request $request) {
    $request->validate([
        'skills' => 'nullable|string|max:1000'
    ]);

    $user = $request->user();
    $user->skills = $request->input('skills');
    $user->save();

    return back()->with('success', 'Skills updated successfully.');
})->middleware(['auth', 'verified'])->name('profile.skills.update');

// Update location (current user)
Route::post('/profile/location', function (\Illuminate\Http\Request $request) {
    $request->validate([
        'location' => 'nullable|string|max:255'
    ]);

    $user = $request->user();
    $user->location = $request->input('location');
    $user->save();

    return back()->with('success', 'Location updated successfully.');
})->middleware(['auth', 'verified'])->name('profile.location.update');

// Update bio (current user)
Route::post('/profile/bio', function (\Illuminate\Http\Request $request) {
    $request->validate([
        'bio' => 'nullable|string|max:2000'
    ]);

    $user = $request->user();
    $user->bio = $request->input('bio');
    $user->save();

    return back()->with('success', 'Bio updated successfully.');
})->middleware(['auth', 'verified'])->name('profile.bio.update');

// Update avatar (current user)
Route::post('/profile/avatar', function (\Illuminate\Http\Request $request) {
    $request->validate([
        'avatar' => 'required|string|max:255'
    ]);

    $avatar = basename($request->input('avatar'));
    $path = public_path('avatars/' . $avatar);

    if (! file_exists($path)) {
        return back()->with('error', 'Invalid avatar selection.');
    }

    $user = $request->user();
    $user->profile_pic = $avatar;
    $user->save();

    return back()->with('success', 'Avatar updated successfully.');
})->middleware(['auth', 'verified'])->name('profile.avatar.update');

// Store Job
Route::post('/jobs', function (\Illuminate\Http\Request $request) {
    $request->merge(['recruiter_id' => auth()->id()]);
    
    $controller = new \App\Http\Controllers\Api\JobController();
    $response = $controller->store($request);

    $status = $response->getStatusCode();

    if ($status === 200) {
        return redirect()->route('jobs.posted')->with('success', 'Job posted successfully!');
    }

    // Handle validation errors returned as JSON from the API controller
    if ($status === 422) {
        $data = json_decode($response->getContent(), true);

        // Standard Laravel validation exception shape: ['message' => ..., 'errors' => [...]]
        if (isset($data['errors']) && is_array($data['errors'])) {
            return back()->withErrors($data['errors'])->withInput();
        }

        // Our custom wage validation returns ['validation' => [...]] with a message
        if (isset($data['validation']) && isset($data['validation']['message'])) {
            return back()->withErrors(['payment' => $data['validation']['message']])->withInput();
        }

        return back()->with('error', 'Failed to post job. Please check your input.')->withInput();
    }

    return back()->with('error', 'Failed to post job. Please try again.')->withInput();
})->middleware(['auth', 'verified'])->name('jobs.store');

// Apply to Job
Route::post('/jobs/{job}/apply', function (\App\Models\Job $job) {
    $existing = \App\Models\JobApplication::where('job_id', $job->id)
        ->where('seeker_id', auth()->id())
        ->first();
    
    if ($existing) {
        return back()->with('error', 'You have already applied to this job.');
    }
    
    \App\Models\JobApplication::create([
        'job_id' => $job->id,
        'seeker_id' => auth()->id(),
        'STATUS' => 'pending'
    ]);
    
    return back()->with('success', 'Application submitted successfully!');
})->middleware(['auth', 'verified'])->name('jobs.apply');

Route::middleware(['auth'])->group(function () {
    Route::redirect('settings', 'settings/profile');

    Volt::route('settings/profile', 'settings.profile')->name('profile.edit');
    Volt::route('settings/password', 'settings.password')->name('user-password.edit');
    Volt::route('settings/appearance', 'settings.appearance')->name('appearance.edit');

    Volt::route('settings/two-factor', 'settings.two-factor')
        ->middleware(
            when(
                Features::canManageTwoFactorAuthentication()
                    && Features::optionEnabled(Features::twoFactorAuthentication(), 'confirmPassword'),
                ['password.confirm'],
                [],
            ),
        )
        ->name('two-factor.show');
});
