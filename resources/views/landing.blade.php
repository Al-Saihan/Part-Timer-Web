<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ config('app.name', 'Part Timer') }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        /* Simple fade-in animation */
        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: translateY(20px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .fade-in {
            animation: fadeIn 0.8s ease-out;
        }
    </style>
</head>

<body class="min-h-screen bg-gradient-to-br from-blue-100 via-blue-50 to-blue-200 text-zinc-900">
    <!-- Main Content -->
    <main class="min-h-screen flex flex-col items-center justify-center px-4">
        <div class="max-w-2xl mx-auto text-center space-y-8 fade-in">
            <!-- Title -->
            <h1 class="text-5xl md:text-6xl font-bold text-blue-900 leading-tight">
                Welcome to <span class="text-blue-700">Part-Timer</span>
            </h1>

            <!-- Description -->
            <p class="text-l md:text-2xl text-blue-800 leading-relaxed max-w-2xl mx-auto">
                Find flexible part-time opportunities that fit your schedule and accelerate your career growth.
            </p>

            <!-- Join Now Button -->
            <div class="pt-4">
                <a href="{{ route('login') }}"
                    class="inline-block bg-blue-600 hover:bg-blue-700 text-white font-semibold text-lg px-10 py-4 rounded-lg shadow-lg transition-all duration-300 transform hover:scale-105 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2">
                    Join Now
                </a>
            </div>
        </div>
    </main>
</body>

</html>