<?php

require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Job;
use App\Http\Controllers\Api\RatingController;

// Find a seeker user (rater) and a recruiter user (rated)
$rater = User::where('user_type', 'seeker')->first();
$rated = User::where('user_type', 'recruiter')->first();
$job = Job::where('recruiter_id', $rated->id)->first();

if (! $rater || ! $rated || ! $job) {
    echo "Missing test data: rater={$rater?->id}, rated={$rated?->id}, job={$job?->id}\n";
    exit(1);
}

$request = Request::create('/api/ratings', 'POST', [
    'rated_user_id' => $rated->id,
    'job_id' => $job->id,
    'rating' => rand(3,5),
    'review' => 'Test rating from tools script'
]);
$request->setUserResolver(function() use ($rater) { return $rater; });

$controller = new RatingController();
$response = $controller->store($request);

echo $response->getContent() . "\n";
