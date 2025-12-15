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

Route::middleware('auth:sanctum')->group(function () {
    // LIST JOBS
    Route::get('/jobs', [JobController::class, 'index']);

    // CREATE JOB
    Route::post('/jobs', [JobController::class, 'store']);

    // APPLY TO JOB
    Route::post('/jobs/{id}/apply', [JobController::class, 'apply']);

    // GET AUTHENTICATED USER
    Route::get('/me', fn (Request $req) => $req->user());
    
    // LOGOUT
    Route::post('/logout', [AuthController::class, 'logout']);
});

// AUTH ROUTES
Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);
