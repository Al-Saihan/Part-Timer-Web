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
    return view('dashboards.seeker');
})->middleware(['auth', 'verified'])->name('seeker.dashboard');

// Recruiter Dashboard
Route::get('/dashboard/recruiter', function () {
    return view('dashboards.recruiter');
})->middleware(['auth', 'verified'])->name('recruiter.dashboard');

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
