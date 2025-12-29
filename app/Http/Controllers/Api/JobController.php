<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Job;
use App\Models\JobApplication;

class JobController extends Controller
{
    // LIST ALL JOBS
    public function index(Request $request)
    {
        $query = Job::select('id', 'title', 'location', 'description', 'difficulty', 'working_hours', 'payment', 'recruiter_id')
            ->with('recruiter:id,name,bio,location,avg_rating,rating_count,profile_pic');
        
        // If user is authenticated, exclude jobs they've already applied to
        if ($request->user()) {
            $appliedJobIds = JobApplication::where('seeker_id', $request->user()->id)
                ->pluck('job_id')
                ->toArray();
            
            $query->whereNotIn('id', $appliedJobIds);
        }
        
        $jobs = $query->get();
        return response()->json($jobs);
    }

    // CREATE NEW JOB (recruiter)
    public function store(Request $request)
    {
        $request->validate([
            'recruiter_id' => 'required|integer',
            'title' => 'required|string',
            'location' => 'nullable|string',
            'description' => 'required|string',
            'difficulty' => 'required|in:easy,medium,hard',
            'working_hours' => 'required|integer',
            'payment' => 'required|numeric',
        ]);

        $difficulty = $request->input('difficulty');
        $hours = (int) $request->input('working_hours');
        $wage = (float) $request->input('payment');

        $validation = $this->validateHourlyWage($difficulty, $hours, $wage);

        // Hard rejection or below standard -> return validation result and do not create job
        if ($validation['status'] !== 'ok') {
            return response()->json([
                'success' => false,
                'validation' => $validation
            ], 422);
        }

        // If authenticated, ensure recruiter_id matches authenticated user
        if ($request->user() && $request->user()->id !== (int) $request->input('recruiter_id')) {
            return response()->json(['message' => 'Forbidden'], 403);
        }

        $job = Job::create($request->all());
        // include recruiter data in the response (selected fields only)
        $job->load('recruiter:id,name,bio,location');

        return response()->json([
            'success' => true,
            'job' => $job
        ]);
    }

    private function getHoursMultiplier(int $hours_per_day): float
    {
        if ($hours_per_day <= 3) {
            return 1.20;
        } elseif ($hours_per_day <= 6) {
            return 1.00;
        } elseif ($hours_per_day <= 8) {
            return 0.90;
        }

        return 0.80;
    }

    private function validateHourlyWage(string $job_difficulty, int $hours_per_day, float $hourly_wage): array
    {
        $MIN_ALLOWED_WAGE = 100;

        $WAGE_STANDARDS = [
            'easy' => ['min' => 100, 'max' => 150],
            'medium' => ['min' => 200, 'max' => 400],
            'hard' => ['min' => 500, 'max' => 1500],
        ];

        $result = [
            'status' => '',
            'recommended_min' => 0,
            'recommended_max' => 0,
            'message' => ''
        ];

        // Step 1: Hard rejection
        if ($hourly_wage < $MIN_ALLOWED_WAGE) {
            $result['status'] = 'rejected';
            $result['message'] = 'Hourly wage below ৳100 is not allowed.';
            return $result;
        }

        // Step 2: Get base standards
        if (! isset($WAGE_STANDARDS[$job_difficulty])) {
            $result['status'] = 'rejected';
            $result['message'] = 'Unknown difficulty level.';
            return $result;
        }

        $base_min = $WAGE_STANDARDS[$job_difficulty]['min'];
        $base_max = $WAGE_STANDARDS[$job_difficulty]['max'];

        // Step 3: Apply hours adjustment
        $hours_multiplier = $this->getHoursMultiplier($hours_per_day);

        $recommended_min = $base_min * $hours_multiplier;
        $recommended_max = $base_max * $hours_multiplier;

        $result['recommended_min'] = (int) round($recommended_min);
        $result['recommended_max'] = (int) round($recommended_max);

        // Step 4: Validate against recommended range
        if ($hourly_wage < $recommended_min) {
            $result['status'] = 'below_standard';
            $result['message'] = 'Offered wage ৳' . $hourly_wage . '/hr is below recommended range (৳' . $result['recommended_min'] . '–৳' . $result['recommended_max'] . '/hr).';
            return $result;
        }

        // Step 5: Wage is acceptable
        $result['status'] = 'ok';
        $result['message'] = 'Hourly wage meets recommended standards.';
        return $result;
    }

    // APPLY TO JOB (seeker)
    public function apply($id, Request $request)
    {
        $validated = $request->validate([
            'seeker_id' => 'required|integer'
        ]);

        $existing = JobApplication::where('job_id', $id)
            ->where('seeker_id', $validated['seeker_id'])
            ->first();
        if ($existing) {
            return response()->json(['message' => 'Already applied'], 400);
        }

        $application = JobApplication::create([
            'job_id' => $id,
            'seeker_id' => $validated['seeker_id'],
            'status' => 'pending'
        ]);

        return response()->json(['message' => 'Applied successfully', 'application' => $application]);
    }

    // GET RECRUITER'S POSTED JOBS
    public function getPostedJobs(Request $request)
    {
        $jobs = Job::where('recruiter_id', $request->user()->id)
            ->with('recruiter:id,name,bio,location,avg_rating,rating_count,profile_pic')
            ->withCount('applications')
            ->latest()
            ->get();
        
        return response()->json($jobs);
    }

    // GET JOBS THE SEEKER HAS APPLIED TO
    public function getAppliedJobs(Request $request)
    {
        $applications = JobApplication::where('seeker_id', $request->user()->id)
            ->with([
                'job:id,title,location,description,difficulty,working_hours,payment,created_at,updated_at',
                'job.recruiter:id,name,bio,location,avg_rating,rating_count,profile_pic'
            ])
            ->latest('created_at')
            ->get(['id','job_id','seeker_id','status','created_at']);

        return response()->json($applications);
    }

    // GET JOB APPLICANTS FOR RECRUITER
    public function getApplicants(Request $request)
    {
        $applicants = JobApplication::whereHas('job', function($query) use ($request) {
            $query->where('recruiter_id', $request->user()->id);
        })
        ->with(['seeker:id,name,email,created_at,bio,location,skills,avg_rating,rating_count,profile_pic', 'job:id,title', 'job.recruiter:id,name,bio,location,avg_rating,rating_count,profile_pic'])
        ->latest('created_at')
        ->get();
        
        return response()->json($applicants);
    }

    // UPDATE AN APPLICATION STATUS (recruiter only)
    public function updateApplicationStatus($id, Request $request)
    {
        $validated = $request->validate([
            'status' => 'required|in:accepted,rejected,pending'
        ]);

        $application = JobApplication::with('job')->find($id);
        if (! $application) {
            return response()->json(['message' => 'Application not found'], 404);
        }

        // Ensure the authenticated user is the job's recruiter
        if ($application->job->recruiter_id !== $request->user()->id) {
            return response()->json(['message' => 'Forbidden'], 403);
        }

        $application->status = $validated['status'];
        $application->save();

        return response()->json(['success' => true, 'application' => $application]);
    }

    // UPDATE A JOB (recruiter) - allows updating location and other fields
    public function update($id, Request $request)
    {
        $validated = $request->validate([
            'title' => 'sometimes|string',
            'location' => 'sometimes|nullable|string',
            'description' => 'sometimes|string',
            'difficulty' => 'sometimes|in:easy,medium,hard',
            'working_hours' => 'sometimes|integer',
            'payment' => 'sometimes|numeric',
        ]);

        $job = Job::find($id);
        if (! $job) {
            return response()->json(['message' => 'Job not found'], 404);
        }

        // Ensure the authenticated user is the job's recruiter
        if ($job->recruiter_id !== $request->user()->id) {
            return response()->json(['message' => 'Forbidden'], 403);
        }

        $job->fill($validated);
        $job->save();

        return response()->json(['success' => true, 'job' => $job]);
    }
}
