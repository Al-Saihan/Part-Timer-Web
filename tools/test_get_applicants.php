<?php

require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Http\Request;
use App\Models\User;
use App\Http\Controllers\Api\JobController;

// Find a recruiter user
$recruiter = User::where('user_type', 'recruiter')->first();
if (! $recruiter) {
    echo "No recruiter user found\n";
    exit(1);
}

$request = Request::create('/api/applicants', 'GET');
$request->setUserResolver(function() use ($recruiter) { return $recruiter; });

$controller = new JobController();
$response = $controller->getApplicants($request);

echo $response->getContent() . "\n";
