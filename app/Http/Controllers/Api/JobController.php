<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Job;
use App\Models\JobApplication;

class JobController extends Controller
{
    // LIST ALL JOBS
    public function index()
    {
        $jobs = Job::select('id', 'title', 'description', 'difficulty', 'working_hours', 'payment')->get();
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
        $request->validate([
            'seeker_id' => 'required|integer'
        ]);

        $existing = JobApplication::where('job_id', $id)
            ->where('seeker_id', $request->seeker_id)
            ->first();
        if ($existing) {
            return response()->json(['message' => 'Already applied'], 400);
        }

        $application = JobApplication::create([
            'job_id' => $id,
            'seeker_id' => $request->seeker_id,
            'STATUS' => 'pending'
        ]);

        return response()->json(['message' => 'Applied successfully', 'application' => $application]);
    }
}
