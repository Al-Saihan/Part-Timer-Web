<?php
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\JobController;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\ChatController;
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

    // UPDATE JOB (recruiter) - partial update including location
    Route::patch('/jobs/{id}', [JobController::class, 'update']);

    // Create or update a rating for a user (authenticated)
    Route::post('/ratings', [\App\Http\Controllers\Api\RatingController::class, 'store']);

    // Ratings: eligible targets for current user
    Route::get('/ratings/eligible', [\App\Http\Controllers\Api\RatingController::class, 'eligible']);

    // Ratings created by current user
    Route::get('/ratings/mine', [\App\Http\Controllers\Api\RatingController::class, 'mine']);

    // Ratings about the current user
    Route::get('/ratings/about-me', [\App\Http\Controllers\Api\RatingController::class, 'aboutMe']);

    // APPLY TO JOB
    Route::post('/jobs/{id}/apply', [JobController::class, 'apply']);

    // GET AUTHENTICATED USER
    Route::get('/me', fn (Request $req) => $req->user());
    
    // UPDATE APPLICATION STATUS (recruiter)
    Route::patch('/applications/{id}/status', [JobController::class, 'updateApplicationStatus']);

    // USER PROFILE UPDATES (authenticated)
    Route::patch('/user/bio', [AuthController::class, 'updateBio']);
    Route::patch('/user/skills', [AuthController::class, 'updateSkills']);
    Route::patch('/user/location', [AuthController::class, 'updateLocation']);
    Route::patch('/user/profile-pic', [AuthController::class, 'updateProfilePic']);
    
    // CHAT / MESSAGING ROUTES
    // Get all chat rooms for authenticated user
    Route::get('/chat/rooms', [ChatController::class, 'getChatRooms']);
    
    // Get or create a chat room with another user
    Route::post('/chat/rooms', [ChatController::class, 'getOrCreateChatRoom']);
    
    // Get chat room details
    Route::get('/chat/rooms/{roomId}', [ChatController::class, 'getChatRoomDetails']);
    
    // Get messages for a specific chat room
    Route::get('/chat/rooms/{roomId}/messages', [ChatController::class, 'getMessages']);
    
    // Send a message in a chat room
    Route::post('/chat/rooms/{roomId}/messages', [ChatController::class, 'sendMessage']);
    
    // Delete a message
    Route::delete('/chat/rooms/{roomId}/messages/{messageId}', [ChatController::class, 'deleteMessage']);
    
    // LOGOUT
    Route::post('/logout', [AuthController::class, 'logout']);
});

// AUTH ROUTES
Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);
Route::post('/password/forgot', [AuthController::class, 'forgotPassword']);
Route::post('/password/reset', [AuthController::class, 'resetPassword']);
