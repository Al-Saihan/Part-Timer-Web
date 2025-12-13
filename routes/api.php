<?php
Route::get('/hello', function () {
    return 'HELLO LARAVEL';
});

Route::get('/ping', function () {
    return response()->json(['status' => 'ok']);
});

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\JobController;
use App\Http\Controllers\Api\AuthController;

// LIST JOBS
Route::get('/jobs', [JobController::class, 'index']);

// CREATE JOB
Route::post('/jobs', [JobController::class, 'store']);

// APPLY TO JOB
Route::post('/jobs/{id}/apply', [JobController::class, 'apply']);

// AUTH ROUTES
Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);
