<?php
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\JobController;
use App\Http\Controllers\Api\AuthController;
use Illuminate\Http\Request;

Route::get('/hello', function () {
    return 'HELLO LARAVEL';
});

Route::get('/ping', function () {
    return response()->json(['status' => 'ok']);
});

Route::middleware('auth:sanctum')->group(function () {
    // LIST JOBS
    Route::get('/jobs', [JobController::class, 'index']);

    // GET APPLIED JOBS (seeker)
    Route::get('/jobs/applied', [JobController::class, 'getAppliedJobs']);

    // GET POSTED JOBS (recruiter)
    Route::get('/jobs/posted', [JobController::class, 'getPostedJobs']);

    // GET JOB APPLICANTS (recruiter)
    Route::get('/applicants', [JobController::class, 'getApplicants']);

    // CREATE JOB
    Route::post('/jobs', [JobController::class, 'store']);

    // APPLY TO JOB
    Route::post('/jobs/{id}/apply', [JobController::class, 'apply']);

    // GET AUTHENTICATED USER
    Route::get('/me', fn (Request $req) => $req->user());
    
    // UPDATE APPLICATION STATUS (recruiter)
    Route::patch('/applications/{id}/status', [JobController::class, 'updateApplicationStatus']);
    
    // LOGOUT
    Route::post('/logout', [AuthController::class, 'logout']);
});

// AUTH ROUTES
Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);
Route::post('/password/forgot', [AuthController::class, 'forgotPassword']);
Route::post('/password/reset', [AuthController::class, 'resetPassword']);
