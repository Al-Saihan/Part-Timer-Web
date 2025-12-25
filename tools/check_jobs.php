<?php

require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Job;

$ids = [23, 24];
$results = [];

foreach ($ids as $id) {
    $job = Job::with('recruiter')->find($id);
    if (! $job) {
        $results[$id] = ['found' => false];
        continue;
    }

    $results[$id] = [
        'found' => true,
        'job' => $job->only(['id','title','location','description','difficulty','working_hours','payment','is_part_time','created_at','updated_at']),
        'recruiter_exists' => (bool) $job->recruiter,
        'recruiter' => $job->recruiter ? $job->recruiter->only(['id','name','email','location','bio','avg_rating','rating_count','profile_pic']) : null,
    ];
}

echo json_encode($results, JSON_PRETTY_PRINT) . "\n";
