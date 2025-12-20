<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Carbon\Carbon;

class DatabaseSeeder extends Seeder
{
    public function run()
    {
        $now = Carbon::now();

        // ---------------------------
        // 1️⃣ Users (3 recruiters, 2 seekers) with Bangla-style names
        // ---------------------------
        $users = [
            ['name' => 'Rahim Ahmed', 'email' => 'rahim@example.com', 'password' => Hash::make('password'), 'user_type' => 'recruiter', 'avg_rating' => 0, 'rating_count' => 0, 'created_at' => $now, 'updated_at' => $now],
            ['name' => 'Karim Hossain', 'email' => 'karim@example.com', 'password' => Hash::make('password'), 'user_type' => 'recruiter', 'avg_rating' => 0, 'rating_count' => 0, 'created_at' => $now, 'updated_at' => $now],
            ['name' => 'Selim Khan', 'email' => 'selim@example.com', 'password' => Hash::make('password'), 'user_type' => 'recruiter', 'avg_rating' => 0, 'rating_count' => 0, 'created_at' => $now, 'updated_at' => $now],
            ['name' => 'Mitu Akter', 'email' => 'mitu@example.com', 'password' => Hash::make('password'), 'user_type' => 'seeker', 'avg_rating' => 0, 'rating_count' => 0, 'created_at' => $now, 'updated_at' => $now],
            ['name' => 'Rana Sultana', 'email' => 'rana@example.com', 'password' => Hash::make('password'), 'user_type' => 'seeker', 'avg_rating' => 0, 'rating_count' => 0, 'created_at' => $now, 'updated_at' => $now],
        ];

        $userIds = [];
        foreach ($users as $user) {
            $userIds[] = DB::table('users')->insertGetId($user);
        }

        // ---------------------------
        // 2️⃣ Jobs (10 jobs randomly assigned to recruiters)
        // ---------------------------
        $titles = [
            'Part-time Data Entry',
            'Evening Tutor (Math/English)',
            'Freelance Graphic Designer',
            'Content Writer (Blog/Article)',
            'Social Media Assistant',
            'Customer Support Executive',
            'Virtual Assistant',
            'Bangla Translator',
            'Marketing Assistant',
            'SEO & Digital Marketing'
        ];

        $difficulties = ['easy', 'medium', 'hard'];

        $jobs = [];
        for ($i = 0; $i < 10; $i++) {
            $jobs[] = [
                'recruiter_id' => $userIds[array_rand([0, 1, 2])], // only recruiters
                'title' => $titles[$i],
                'description' => 'This is the description for ' . $titles[$i],
                'difficulty' => $difficulties[array_rand($difficulties)],
                'working_hours' => rand(2, 8),
                'payment' => rand(500, 5000), // BDT
                'is_part_time' => 1,
                'created_at' => $now,
                'updated_at' => $now,
            ];
        }

        $jobIds = [];
        foreach ($jobs as $job) {
            $jobIds[] = DB::table('jobs')->insertGetId($job);
        }

        // ---------------------------
        // 3️⃣ Job Applications (random by seekers)
        // ---------------------------
        $seekers = array_slice($userIds, 3, 2); // seeker IDs

        $applications = [];
        foreach ($jobIds as $jobId) {
            foreach ($seekers as $seekerId) {
                if (rand(0, 1)) {
                    $applications[] = [
                        'job_id' => $jobId,
                        'seeker_id' => $seekerId,
                        'status' => ['pending', 'accepted', 'rejected'][rand(0, 2)],
                        'created_at' => $now,
                        'updated_at' => $now,
                    ];
                }
            }
        }

        DB::table('job_applications')->insert($applications);
    }
}
