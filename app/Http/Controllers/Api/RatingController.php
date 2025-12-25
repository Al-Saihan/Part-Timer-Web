<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\UserRating;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use App\Models\JobApplication;
use App\Models\Job;

class RatingController extends Controller
{
    // Create or update a rating for a user (upsert)
    public function store(Request $request)
    {
        $user = $request->user();
        if (! $user) {
            return response()->json(['message' => 'Unauthenticated'], 401);
        }

        $validated = $request->validate([
            'rated_user_id' => 'required|integer|exists:users,id',
            'job_id' => 'required|integer|exists:jobs,id',
            'rating' => 'required|integer|min:1|max:5',
            'review' => 'sometimes|nullable|string',
        ]);

        $raterId = $user->id;
        $ratedUserId = $validated['rated_user_id'];
        $jobId = $validated['job_id'];

        // ENFORCE ELIGIBILITY: recruiter can rate accepted seekers for their jobs;
        // seeker can rate recruiters for jobs where they were accepted.
        $isAllowed = false;
        if ($user->id === $ratedUserId) {
            return response()->json(['message' => 'Cannot rate yourself'], 400);
        }

        if ($user->user_type === 'recruiter') {
            // recruiter rating a seeker
            $isAllowed = JobApplication::where('job_id', $jobId)
                ->where('seeker_id', $ratedUserId)
                ->where('status', 'accepted')
                ->whereHas('job', function($q) use ($user) {
                    $q->where('recruiter_id', $user->id);
                })->exists();
        } else {
            // seeker rating a recruiter
            $isAllowed = JobApplication::where('job_id', $jobId)
                ->where('seeker_id', $user->id)
                ->where('status', 'accepted')
                ->whereHas('job', function($q) use ($ratedUserId) {
                    $q->where('recruiter_id', $ratedUserId);
                })->exists();
        }

        if (! $isAllowed) {
            return response()->json(['message' => 'Not allowed to rate this user for this job'], 403);
        }

        // Use transaction to keep ratings and user aggregate consistent
        DB::beginTransaction();
        try {
            $rating = UserRating::where('rater_id', $raterId)
                ->where('rated_user_id', $ratedUserId)
                ->where('job_id', $jobId)
                ->first();

            if ($rating) {
                $rating->rating = $validated['rating'];
                $rating->review = $validated['review'] ?? $rating->review;
                $rating->save();
                $action = 'updated';
            } else {
                $rating = UserRating::create([
                    'rater_id' => $raterId,
                    'rated_user_id' => $ratedUserId,
                    'job_id' => $jobId,
                    'rating' => $validated['rating'],
                    'review' => $validated['review'] ?? null,
                ]);
                $action = 'created';
            }

            // Recalculate aggregates for the rated user
            $agg = UserRating::where('rated_user_id', $ratedUserId)
                ->selectRaw('AVG(rating) as avg_rating, COUNT(*) as rating_count')
                ->first();

            $userModel = User::find($ratedUserId);
            if ($userModel) {
                $userModel->avg_rating = $agg->avg_rating ? round($agg->avg_rating, 2) : 0.00;
                $userModel->rating_count = (int) $agg->rating_count;
                $userModel->save();
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'action' => $action,
                'rating' => $rating,
                'rated_user' => $userModel ? $userModel->only(['id','name','avg_rating','rating_count']) : null,
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['message' => 'Failed to save rating', 'error' => $e->getMessage()], 500);
        }
    }

    // GET eligible rating targets for the authenticated user
    public function eligible(Request $request)
    {
        $user = $request->user();
        if (! $user) return response()->json([], 200);

        $results = [];

        if ($user->user_type === 'recruiter') {
            // recruiter: list accepted seekers for jobs they own
            $apps = JobApplication::whereHas('job', function($q) use ($user) {
                    $q->where('recruiter_id', $user->id);
                })
                ->where('status', 'accepted')
                ->with(['seeker:id,name,profile_pic,avg_rating,rating_count', 'job:id,title'])
                ->get();

            foreach ($apps as $app) {
                $existing = UserRating::where('rater_id', $user->id)
                    ->where('rated_user_id', $app->seeker_id)
                    ->where('job_id', $app->job_id)
                    ->first();

                $results[] = [
                    'job_id' => $app->job_id,
                    'job_title' => $app->job->title ?? null,
                    'other_user' => $app->seeker,
                    'role' => 'seeker',
                    'can_rate' => $existing ? false : true,
                    'existing_rating' => $existing,
                ];
            }
        } else {
            // seeker: list recruiters for accepted applications
            $apps = JobApplication::where('seeker_id', $user->id)
                ->where('status', 'accepted')
                ->with(['job:id,title,recruiter_id', 'job.recruiter:id,name,profile_pic,avg_rating,rating_count'])
                ->get();

            foreach ($apps as $app) {
                $recruiter = $app->job->recruiter ?? null;
                if (! $recruiter) continue;

                $existing = UserRating::where('rater_id', $user->id)
                    ->where('rated_user_id', $recruiter->id)
                    ->where('job_id', $app->job_id)
                    ->first();

                $results[] = [
                    'job_id' => $app->job_id,
                    'job_title' => $app->job->title ?? null,
                    'other_user' => $recruiter,
                    'role' => 'recruiter',
                    'can_rate' => $existing ? false : true,
                    'existing_rating' => $existing,
                ];
            }
        }

        return response()->json($results);
    }

    // GET ratings created by the authenticated user
    public function mine(Request $request)
    {
        $user = $request->user();
        if (! $user) return response()->json([], 200);

        $ratings = UserRating::where('rater_id', $user->id)
            ->with(['ratedUser:id,name,profile_pic,avg_rating,rating_count','job:id,title'])
            ->latest()
            ->get();

        return response()->json($ratings);
    }

    // GET ratings about the authenticated user
    public function aboutMe(Request $request)
    {
        $user = $request->user();
        if (! $user) return response()->json([], 200);

        $ratings = UserRating::where('rated_user_id', $user->id)
            ->with(['rater:id,name,profile_pic,avg_rating,rating_count','job:id,title'])
            ->latest()
            ->get();

        return response()->json($ratings);
    }
}
