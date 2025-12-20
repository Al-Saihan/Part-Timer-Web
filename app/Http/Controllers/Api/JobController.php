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
        $query = Job::select('id', 'title', 'description', 'difficulty', 'working_hours', 'payment');
        
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
            'description' => 'required|string',
            'difficulty' => 'required|in:easy,medium,hard',
            'working_hours' => 'required|integer',
            'payment' => 'required|numeric',
        ]);

        $job = Job::create($request->all());

        return response()->json([
            'success' => true,
            'job' => $job
        ]);
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
            ->withCount('applications')
            ->latest()
            ->get();
        
        return response()->json($jobs);
    }

    // GET JOBS THE SEEKER HAS APPLIED TO
    public function getAppliedJobs(Request $request)
    {
        $applications = JobApplication::where('seeker_id', $request->user()->id)
            ->with(['job:id,title,description,difficulty,working_hours,payment,created_at,updated_at'])
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
        ->with(['seeker:id,name,email,created_at', 'job:id,title'])
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
}
