<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    @include('partials.head')
</head>

<body class="min-h-screen bg-gradient-to-br from-blue-100 via-blue-50 to-blue-200 text-zinc-900 antialiased">
    <!-- Decorative elements -->
    <div class="absolute inset-0 overflow-hidden">
        <div class="absolute -top-40 -right-40 h-80 w-80 rounded-full bg-blue-200 opacity-30 blur-3xl"></div>
        <div class="absolute -bottom-40 -left-40 h-80 w-80 rounded-full bg-blue-300 opacity-30 blur-3xl"></div>
    </div>

    <div class="relative flex min-h-svh flex-col items-center justify-center p-6 md:p-10">
        <!-- Main card container -->
        <div class="w-full max-w-md">
            <!-- Logo/Brand section -->
            <div class="mb-10 text-center">
                <a href="{{ route('home') }}" class="inline-block transition-transform hover:scale-105">
                    <span class="text-5xl font-bold text-blue-900 md:text-6xl">
                        Part Timer
                    </span>
                </a>
                <p class="mt-3 text-sm text-blue-800 opacity-80">
                    Find your perfect part-time opportunity
                </p>
            </div>

            <!-- Content card with subtle shadow -->
            <div class="rounded-xl bg-white p-8 shadow-lg">
                {{ $slot }}
            </div>
        </div>
    </div>
</body>

</html>