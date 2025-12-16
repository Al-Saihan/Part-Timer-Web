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
Route::get('/dashboard/recruiter', function () {
    return view('dashboards.recruiter');
})->middleware(['auth', 'verified'])->name('recruiter.dashboard');

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
