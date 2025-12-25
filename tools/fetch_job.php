<?php

require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Job;

$job = Job::with('recruiter')->first();
if (! $job) {
    echo "No jobs found\n";
    exit(0);
}

echo $job->toJson(JSON_PRETTY_PRINT) . "\n";
