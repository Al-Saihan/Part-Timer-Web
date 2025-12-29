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
