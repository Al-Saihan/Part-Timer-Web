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
            ['name' => 'Rahim Ahmed', 'email' => 'rahim@example.com', 'password' => Hash::make('password'), 'user_type' => 'recruiter', 'bio' => 'Experienced recruiter in Dhaka region', 'location' => 'Dhaka', 'avg_rating' => 0, 'rating_count' => 0, 'created_at' => $now, 'updated_at' => $now],
            ['name' => 'Karim Hossain', 'email' => 'karim@example.com', 'password' => Hash::make('password'), 'user_type' => 'recruiter', 'bio' => 'Small business owner hiring part-timers', 'location' => 'Chittagong', 'avg_rating' => 0, 'rating_count' => 0, 'created_at' => $now, 'updated_at' => $now],
            ['name' => 'Selim Khan', 'email' => 'selim@example.com', 'password' => Hash::make('password'), 'user_type' => 'recruiter', 'bio' => 'Freelance project coordinator', 'location' => 'Rajshahi', 'avg_rating' => 0, 'rating_count' => 0, 'created_at' => $now, 'updated_at' => $now],
            ['name' => 'Mitu Akter', 'email' => 'mitu@example.com', 'password' => Hash::make('password'), 'user_type' => 'seeker', 'bio' => 'University student available evenings', 'location' => 'Dhaka', 'skills' => 'tutoring, data entry, ms office', 'avg_rating' => 0, 'rating_count' => 0, 'created_at' => $now, 'updated_at' => $now],
            ['name' => 'Rana Sultana', 'email' => 'rana@example.com', 'password' => Hash::make('password'), 'user_type' => 'seeker', 'bio' => 'Home-based worker with admin skills', 'location' => 'Sylhet', 'skills' => 'administration, ms office, customer support', 'avg_rating' => 0, 'rating_count' => 0, 'created_at' => $now, 'updated_at' => $now],
        ];

        // Assign a randomized profile_pic for each user in the format
        // "Avatars Set Flat Style-XX" where XX is 01..50
        foreach ($users as &$u) {
            $u['profile_pic'] = 'Avatars Set Flat Style-' . rand(1, 50);
        }
        unset($u);

        // Remove any existing users with these emails to avoid unique constraint errors
        $emails = array_map(function ($u) { return $u['email']; }, $users);
        DB::table('users')->whereIn('email', $emails)->delete();

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

        // Human-friendly, role-specific descriptions for seeded jobs
        $descriptions = [
            'Perform accurate data entry and record-keeping. Must be comfortable with Excel/Google Sheets, able to follow templates, and maintain data quality. Suitable for detail-oriented part-timers; occasional remote work.',
            'Provide tutoring in Math or English for secondary-level students in the evenings. Must be patient, explain concepts clearly, prepare short lesson plans, and assess progress. Ideal for university students or teachers.',
            'Create branding assets, social posts, and simple marketing collateral. Requires experience with Adobe Photoshop/Illustrator or equivalent, a portfolio of past work, and the ability to meet deadlines for short freelance projects.',
            'Write clear, engaging blog posts and short articles (400–1,200 words). Topics vary; good grammar, research skills, and ability to adapt tone are required. SEO-aware writers preferred.',
            'Manage social media posts, schedule content, engage with comments/messages, and report simple analytics. Familiarity with Facebook and Instagram business tools is a plus.',
            'Handle customer inquiries via phone, chat, or email. Good communication skills, patience, and basic problem-solving are required. Shift work possible; training provided.',
            'Support daily administrative tasks: email handling, calendar management, simple data lookups, and light research. Strong organizational skills and reliability required for remote part-time work.',
            'Translate short documents and conversational text between English and Bangla while preserving meaning and tone. Attention to idioms and locality required; experience preferred.',
            'Assist with local marketing activities: outreach, simple campaign execution, and collecting feedback. Good communication, basic MS Office skills, and willingness to do light field work are desirable.',
            'Improve website visibility through on-page SEO, keyword research, and basic analytics reporting. Prior experience with Google Analytics/Search Console and CMS is helpful.'
        ];

        $difficulties = ['easy', 'medium', 'hard'];

        $locations = ['Dhaka', 'Chittagong', 'Sylhet', 'Rajshahi', 'Khulna', 'Rangpur', 'Mymensingh', 'Gazipur', 'Comilla', 'Barisal'];

        // pick only recruiter IDs (first three users inserted)
        $recruiterIds = array_slice($userIds, 0, 3);

        $jobs = [];
        for ($i = 0; $i < 10; $i++) {
            $jobs[] = [
                'recruiter_id' => $recruiterIds[array_rand($recruiterIds)], // only recruiters
                'title' => $titles[$i],
                'location' => $locations[array_rand($locations)],
                'description' => $descriptions[$i],
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
