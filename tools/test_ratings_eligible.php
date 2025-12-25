<?php

require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Http\Request;
use App\Models\User;
use App\Http\Controllers\Api\RatingController;

$controller = new RatingController();

// Test as first recruiter
$recruiter = User::where('user_type', 'recruiter')->first();
if ($recruiter) {
    $req = Request::create('/api/ratings/eligible', 'GET');
    $req->setUserResolver(fn() => $recruiter);
    echo "=== Eligible for recruiter {$recruiter->id} ({$recruiter->name}) ===\n";
    echo $controller->eligible($req)->getContent() . "\n";
}

// Test as first seeker
$seeker = User::where('user_type', 'seeker')->first();
if ($seeker) {
    $req = Request::create('/api/ratings/eligible', 'GET');
    $req->setUserResolver(fn() => $seeker);
    echo "=== Eligible for seeker {$seeker->id} ({$seeker->name}) ===\n";
    echo $controller->eligible($req)->getContent() . "\n";
}
